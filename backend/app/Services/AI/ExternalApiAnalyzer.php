<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;

class ExternalApiAnalyzer implements AnalyzerInterface
{
    public function analyze(string $text): array
    {
        $url = config('ai.external.url');
        $key = config('ai.external.key');
        $timeout = config('ai.external.timeout');

        if (!$url) {
            return (new RuleBasedAnalyzer())->analyze($text);
        }

        $res = Http::timeout($timeout)
            ->withToken($key)
            ->post($url, ['text' => $text]);

        if (!$res->ok()) {
            return (new RuleBasedAnalyzer())->analyze($text);
        }

        $data = $res->json();

        return [
            'sentiment' => (string)($data['sentiment'] ?? 'neutral'),
            'score' => (int)($data['score'] ?? 50),
            'topics' => array_values((array)($data['topics'] ?? [])),
        ];
    }
}
