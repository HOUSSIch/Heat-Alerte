<?php

namespace Tests\Feature;

use App\Services\HeatAlertAiService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class HeatAlertAiServiceTest extends TestCase
{
    public function test_signalement_parser_accepts_a_fenced_json_response(): void
    {
        config()->set('services.gemini.key', 'test-key');
        config()->set('services.gemini.model', 'test-model');
        Http::fake(['generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [[
                'content' => [
                    'parts' => [[
                        'text' => "```json\n{\"titre\":\"Panne locale\",\"description\":\"Coupure constatée.\",\"adresse\":null,\"quartier\":\"Bardo\"}\n```",
                    ]],
                ],
            ]],
        ])]);

        $parsed = app(HeatAlertAiService::class)->parseSignalement('Plus de courant à Bardo.');

        $this->assertSame('Panne locale', $parsed['titre']);
        $this->assertSame('Coupure constatée.', $parsed['description']);
        $this->assertNull($parsed['adresse']);
        $this->assertSame('Bardo', $parsed['quartier']);
    }

    public function test_assistant_reports_quota_and_unavailable_responses(): void
    {
        config()->set('services.gemini.key', 'test-key');
        config()->set('services.gemini.model', 'test-model');

        Http::fake(['generativelanguage.googleapis.com/*' => Http::response([], 429)]);
        try {
            app(HeatAlertAiService::class)->ask('Bonjour');
            $this->fail('The quota response must throw an exception.');
        } catch (\RuntimeException $exception) {
            $this->assertStringContainsString('quota', $exception->getMessage());
        }

        Http::fake(['generativelanguage.googleapis.com/*' => Http::response([], 500)]);
        $this->expectException(\RuntimeException::class);
        app(HeatAlertAiService::class)->ask('Bonjour');
    }
}
