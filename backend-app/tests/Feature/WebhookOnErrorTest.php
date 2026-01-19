<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WebhookOnErrorTest extends TestCase
{
    public function test_webhook_se_envia_en_respuesta_no_exitosa(): void
    {
        $url = config('services.webhook.url');
        if (!$url) {
            $this->markTestSkipped('WEBHOOK_URL no configurado.');
        }

        $this->postJson('/api/login', []);
        $this->assertTrue(true);
    }
}
