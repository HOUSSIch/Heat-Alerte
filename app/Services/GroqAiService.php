<?php

namespace App\Services;

use App\Models\PublicationQuartier;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqAiService
{
    /**
     * @return array{resume: string, probleme_principal: string, situation: string, points_cles: array<int, string>}
     */
    public function summarizeQuartierDiscussion(PublicationQuartier $publication): array
    {
        if (blank(config('services.groq.key'))) {
            throw new \RuntimeException('Le résumé IA est temporairement indisponible.');
        }

        $publication->loadMissing('commentaires');
        $payload = [
            'titre' => $publication->titre,
            'contenu' => $publication->contenu,
            'type' => $publication->type,
            'statut' => $publication->statut,
            'commentaires' => $publication->commentaires->sortBy('created_at')->values()->map(
                fn ($commentaire) => ['contenu' => $commentaire->contenu],
            )->all(),
        ];
        $key = 'groq:discussion:'.hash('sha256', (string) json_encode($payload, JSON_UNESCAPED_UNICODE));

        return Cache::remember($key, now()->addHour(), function () use ($payload, $publication): array {
            $data = $this->requestJson(
                'Tu résumes une seule discussion HeatAlert. Retourne UNIQUEMENT un objet JSON valide avec exactement les clés "resume", "probleme_principal", "situation", "points_cles". "points_cles" est un tableau de courtes chaînes. N’invente jamais une intervention, heure, adresse, cause ou résolution : si une donnée est incertaine, formule-la prudemment. Données de la seule discussion : '
                    .json_encode($payload, JSON_UNESCAPED_UNICODE),
                450,
                'Le résumé IA est temporairement indisponible.',
                ['feature' => 'discussion_summary', 'publication_id' => $publication->id, 'comments_count' => count($payload['commentaires'])],
            );

            return $this->validateDiscussionSummary($data);
        });
    }

    public function analyse(int $pointId, Collection $avis): array
    {
        if (blank(config('services.groq.key'))) {
            throw new \RuntimeException('Le service d’analyse IA est temporairement indisponible.');
        }

        $items = $avis->take(20)->map(fn ($avis) => [
            'id' => $avis->id,
            'note' => $avis->note,
            'commentaire' => $avis->commentaire,
        ])->values()->all();

        if ($items === []) {
            throw new \RuntimeException('Aucun avis à analyser.');
        }

        $key = 'groq:avis:'.$pointId.':'.hash('sha256', (string) json_encode($items));

        return Cache::remember($key, now()->addHour(), function () use ($items, $pointId): array {
            Log::info('Groq analysis started', [
                'point_id' => $pointId,
                'reviews_count' => count($items),
                'model' => config('services.groq.model', 'openai/gpt-oss-20b'),
            ]);

            return $this->requestJson(
                'Analyse ces avis HeatAlert et retourne UNIQUEMENT JSON: sentiment_global,resume,points_positifs,problemes_frequents,avis_a_verifier. Avis: '
                    .json_encode($items, JSON_UNESCAPED_UNICODE),
                400,
                'Le service d’analyse IA est temporairement indisponible.',
                ['point_id' => $pointId],
            );
        });
    }

    /**
     * Structure un texte de signalement sans créer ni modifier de donnée.
     *
     * @return array{titre: string, description: string, adresse: ?string, quartier: ?string}
     */
    public function parseSignalement(string $text): array
    {
        if (blank(config('services.groq.key'))) {
            throw new \RuntimeException('Assistant HeatAlert temporairement indisponible.');
        }

        $prompt = <<<'PROMPT'
Tu structures un signalement HeatAlert de coupure électrique.
Retourne UNIQUEMENT un objet JSON valide avec exactement ces clés :
"titre", "description", "adresse", "quartier".

Règles :
- reste strictement fidèle au texte fourni ;
- crée un titre court ;
- reformule la description proprement sans ajouter de fait ;
- adresse et quartier doivent être null s'ils ne sont pas explicitement identifiables ;
- n'invente jamais une adresse, un quartier, une durée ou une information ;
- ne crée aucun signalement et ne donne aucun conseil.

Texte utilisateur :
PROMPT;

        $data = $this->requestJson(
            $prompt."\n".$text,
            350,
            'Assistant HeatAlert temporairement indisponible.',
            ['feature' => 'signalement_fill'],
        );

        return $this->validateSignalementFields($data);
    }

    private function requestJson(string $prompt, int $maxTokens, string $unavailableMessage, array $context = []): array
    {
        $started = microtime(true);

        if (($context['feature'] ?? null) === 'signalement_fill') {
            Log::info('[AI-SIGNAL] Groq request start', [
                'model' => config('services.groq.model', 'openai/gpt-oss-20b'),
            ]);
        }

        try {
            $response = Http::connectTimeout(5)
                ->timeout(20)
                ->withToken(config('services.groq.key'))
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => config('services.groq.model', 'openai/gpt-oss-20b'),
                    'messages' => [['role' => 'user', 'content' => $prompt]],
                    'reasoning_effort' => 'low',
                    'max_tokens' => $maxTokens,
                ]);
        } catch (\Throwable $exception) {
            Log::warning('Groq request failed', $context + [
                'type' => $exception::class,
                'duration_ms' => (int) ((microtime(true) - $started) * 1000),
            ]);

            if (($context['feature'] ?? null) === 'signalement_fill') {
                Log::error('[AI-SIGNAL] Groq failed', [
                    'message' => 'Connection to Groq failed.',
                    'duration_ms' => (int) ((microtime(true) - $started) * 1000),
                ]);
            }

            if (in_array($context['feature'] ?? null, ['signalement_fill', 'discussion_summary'], true)
                && $exception instanceof ConnectionException
                && str_contains(mb_strtolower($exception->getMessage()), 'timed out')) {
                throw new \RuntimeException('Le service IA met trop de temps à répondre. Veuillez réessayer.');
            }

            throw new \RuntimeException($unavailableMessage);
        }

        Log::info('Groq response received', $context + [
            'status' => $response->status(),
            'duration_ms' => (int) ((microtime(true) - $started) * 1000),
        ]);

        if (($context['feature'] ?? null) === 'signalement_fill') {
            Log::info('[AI-SIGNAL] Groq HTTP response', [
                'status' => $response->status(),
                'duration_ms' => (int) ((microtime(true) - $started) * 1000),
            ]);
        }

        if ($response->status() === 429) {
            if (($context['feature'] ?? null) === 'signalement_fill') {
                Log::error('[AI-SIGNAL] Groq failed', [
                    'status' => 429,
                    'message' => 'Groq quota reached.',
                ]);
                throw new \RuntimeException('Le quota du service IA de saisie est temporairement atteint.');
            }

            if (($context['feature'] ?? null) === 'discussion_summary') {
                throw new \RuntimeException('Le quota du service de résumé IA est temporairement atteint. Veuillez réessayer plus tard.');
            }

            throw new \RuntimeException('Le quota du service d’analyse IA est temporairement atteint. Veuillez réessayer plus tard.');
        }

        $content = data_get($response->json(), 'choices.0.message.content');

        if (! $response->successful() || ! is_string($content)) {
            throw new \RuntimeException($unavailableMessage);
        }

        $json = preg_replace('/^\x60{3}(?:json)?\s*/', '', trim($content)) ?? '';
        $json = trim(preg_replace('/\s*\x60{3}$/', '', $json) ?? '');
        $start = strpos($json, '{');
        $end = strrpos($json, '}');

        if ($start !== false && $end !== false) {
            $json = substr($json, $start, $end - $start + 1);
        }

        $data = json_decode($json, true);

        if (! is_array($data)) {
            if (($context['feature'] ?? null) === 'signalement_fill') {
                Log::error('[AI-SIGNAL] Groq failed', [
                    'status' => $response->status(),
                    'message' => 'Invalid JSON response.',
                ]);
            }

            throw new \RuntimeException('Impossible d’interpréter la réponse du service IA.');
        }

        if (($context['feature'] ?? null) === 'signalement_fill') {
            Log::info('[AI-SIGNAL] Groq JSON parsed');
        }

        return $data;
    }

    /**
     * @param  array<mixed>  $data
     * @return array{titre: string, description: string, adresse: ?string, quartier: ?string}
     */
    private function validateSignalementFields(array $data): array
    {
        $allowedKeys = ['titre', 'description', 'adresse', 'quartier'];

        if (array_diff(array_keys($data), $allowedKeys) !== [] || array_diff($allowedKeys, array_keys($data)) !== []) {
            throw new \RuntimeException('Assistant HeatAlert temporairement indisponible.');
        }

        $title = $this->nullableString($data['titre'] ?? null, 255);
        $description = $this->nullableString($data['description'] ?? null, 5000);
        $address = $this->nullableString($data['adresse'] ?? null, 255);
        $quartier = $this->nullableString($data['quartier'] ?? null, 255);

        if ($title === null || $description === null) {
            throw new \RuntimeException('Assistant HeatAlert temporairement indisponible.');
        }

        return [
            'titre' => $title,
            'description' => $description,
            'adresse' => $address,
            'quartier' => $quartier,
        ];
    }

    private function nullableString(mixed $value, int $maximumLength): ?string
    {
        if ($value === null) {
            return null;
        }

        if (! is_string($value)) {
            throw new \RuntimeException('Assistant HeatAlert temporairement indisponible.');
        }

        $value = trim($value);

        if ($value === '') {
            return null;
        }

        if (mb_strlen($value) > $maximumLength) {
            throw new \RuntimeException('Assistant HeatAlert temporairement indisponible.');
        }

        return $value;
    }

    /**
     * @param  array<mixed>  $data
     * @return array{resume: string, probleme_principal: string, situation: string, points_cles: array<int, string>}
     */
    private function validateDiscussionSummary(array $data): array
    {
        $keys = ['resume', 'probleme_principal', 'situation', 'points_cles'];

        if (array_diff(array_keys($data), $keys) !== [] || array_diff($keys, array_keys($data)) !== []) {
            throw new \RuntimeException('Impossible d’interpréter la réponse du service IA.');
        }

        $summary = [];
        foreach (['resume', 'probleme_principal', 'situation'] as $key) {
            $value = $this->nullableString($data[$key] ?? null, 2000);
            if ($value === null) {
                throw new \RuntimeException('Impossible d’interpréter la réponse du service IA.');
            }
            $summary[$key] = $value;
        }

        if (! is_array($data['points_cles']) || count($data['points_cles']) > 8) {
            throw new \RuntimeException('Impossible d’interpréter la réponse du service IA.');
        }

        $summary['points_cles'] = array_values(array_map(function (mixed $point): string {
            $value = $this->nullableString($point, 500);
            if ($value === null) {
                throw new \RuntimeException('Impossible d’interpréter la réponse du service IA.');
            }

            return $value;
        }, $data['points_cles']));

        return $summary;
    }
}
