<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $rules): Response
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $ruleList = array_filter(array_map('trim', explode('|', $rules)));
        foreach ($ruleList as $rule) {
            $parts = array_map('trim', explode(':', $rule));
            if (count($parts) < 3) {
                continue;
            }

            $module = array_shift($parts);
            $action = array_pop($parts);
            $section = implode(':', $parts);
            if ($user->hasPermission($module, $section, $action)) {
                return $next($request);
            }

            if ($user->hasPermissionWithSectionPrefix($module, $section . ':', $action)) {
                return $next($request);
            }
        }

        return response()->json(['message' => 'Forbidden'], 403);
    }
}
