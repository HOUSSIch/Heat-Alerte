<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class HeatAlertAiService
{
    /** @return array{titre: string, description: string, adresse: ?string, quartier: ?string} */
    public function parseSignalement(string $text): array
    {
        $prompt = 'Extrais un signalement de coupure électrique. Réponds uniquement avec un JSON valide contenant titre, description, adresse (null si absente), quartier (null si absent). N’invente jamais une adresse. Texte : '.$text;
        $answer = $this->ask($prompt);
        $json = preg_replace('/^(?:```(?:json)?)|(?:```$)/m', '', trim($answer));
        $data = json_decode($json, true, flags: JSON_THROW_ON_ERROR);

        if (! is_array($data) || ! is_string($data['titre'] ?? null) || ! is_string($data['description'] ?? null)) {
            throw new \RuntimeException('Assistant HeatAlert temporairement indisponible.');
        }

        return [
            'titre' => mb_substr($data['titre'], 0, 255),
            'description' => mb_substr($data['description'], 0, 5000),
            'adresse' => is_string($data['adresse'] ?? null) ? mb_substr($data['adresse'], 0, 255) : null,
            'quartier' => is_string($data['quartier'] ?? null) ? mb_substr($data['quartier'], 0, 255) : null,
        ];
    }

    public function ask(string $prompt): string
    {
        if (! config('services.gemini.key')) {
            throw new \RuntimeException('La configuration du service Assistant HeatAlert est incomplète.');
        }

        $url = 'https://generativelanguage.googleapis.com/v1beta/models/'.config('services.gemini.model').':generateContent?key='.config('services.gemini.key');
        $response = Http::timeout(20)->post($url, [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                    ],
                ],
            ],
        ]);
        $answer = data_get($response->json(), 'candidates.0.content.parts.0.text');

        if ($response->status() === 429) {
            throw new \RuntimeException('Le quota du service Assistant HeatAlert est atteint. Veuillez réessayer plus tard.');
        }

        if (! $response->successful() || ! is_string($answer)) {
            throw new \RuntimeException('Le service Assistant HeatAlert est momentanément indisponible.');
        }

        return $answer;
    }
}
