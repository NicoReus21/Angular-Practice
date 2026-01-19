<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class WebhookOnError
{
    public function handle(Request $request, Closure $next): Response
    {
        $start = microtime(true);

        try {
            $response = $next($request);
        } catch (\Throwable $e) {
            $this->sendWebhook($request, null, $start, $e);
            throw $e;
        }

        $status = $response->getStatusCode();
        if (!in_array($status, [200, 201], true)) {
            $this->sendWebhook($request, $response, $start);
        }

        return $response;
    }

    private function sendWebhook(Request $request, ?Response $response, float $start, ?\Throwable $exception = null): void
    {
        $url = config('services.webhook.url') ?: env('WEBHOOK_URL');
        if (!$url) {
            return;
        }

        try {
            $user = $request->user();
            $payload = [
                'status' => $response?->getStatusCode(),
                'method' => $request->method(),
                'path' => $request->path(),
                'full_url' => $request->fullUrl(),
                'route_name' => optional($request->route())->getName(),
                'duration_ms' => (int) ((microtime(true) - $start) * 1000),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'user' => $user ? [
                    'id' => $user->id,
                    'email' => $user->email,
                    'name' => $user->name,
                ] : null,
                'query' => $request->query(),
                'input' => $this->sanitizeInput($request->all()),
                'exception' => $exception ? [
                    'class' => get_class($exception),
                    'message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                ] : null,
            ];

            if ($this->isDiscordWebhook($url)) {
                $message = sprintf(
                    '[API] %s %s -> %s',
                    $payload['method'],
                    $payload['path'],
                    $payload['status'] ?? 'error'
                );
                $discordPayload = [
                    'content' => $message,
                    'embeds' => [
                        [
                            'title' => 'Request details',
                            'fields' => [
                                ['name' => 'Status', 'value' => (string) ($payload['status'] ?? 'error'), 'inline' => true],
                                ['name' => 'Route', 'value' => $payload['path'], 'inline' => true],
                                ['name' => 'Method', 'value' => $payload['method'], 'inline' => true],
                                ['name' => 'User', 'value' => $payload['user']['email'] ?? 'guest', 'inline' => true],
                                ['name' => 'IP', 'value' => $payload['ip'] ?? 'unknown', 'inline' => true],
                                ['name' => 'Duration', 'value' => $payload['duration_ms'] . 'ms', 'inline' => true],
                            ],
                        ],
                    ],
                ];

                if ($payload['exception']) {
                    $discordPayload['embeds'][0]['fields'][] = [
                        'name' => 'Exception',
                        'value' => $payload['exception']['class'] . ': ' . $payload['exception']['message'],
                        'inline' => false,
                    ];
                }

                Http::timeout(2)->post($url, $discordPayload);
                return;
            }

            Http::timeout(2)->post($url, $payload);
        } catch (\Throwable $e) {
            // No interrumpir la request si falla el webhook.
        }
    }

    private function isDiscordWebhook(string $url): bool
    {
        return str_contains($url, 'discord.com/api/webhooks');
    }

    private function sanitizeInput(array $input): array
    {
        $redactedKeys = ['password', 'password_confirmation', 'token', 'authorization'];
        $clean = [];

        foreach ($input as $key => $value) {
            if (in_array($key, $redactedKeys, true)) {
                $clean[$key] = '[redacted]';
                continue;
            }

            if (is_string($value)) {
                $clean[$key] = Str::limit($value, 1000);
                continue;
            }

            if (is_array($value)) {
                $clean[$key] = '[array]';
                continue;
            }

            $clean[$key] = $value;
        }

        return $clean;
    }
}
