<?php

namespace App\Services\AI;

class RuleBasedAnalyzer implements AnalyzerInterface
{
    private array $positive = [
        'excellent','parfait','super','top','rapide','bon','bonne','incroyable','génial',
        'satisfait','satisfaite','recommande','nickel'
    ];

    private array $negative = [
        'nul','mauvais','horrible','lent','déçu','déçue','arnaque','cassé','retard',
        'catastrophe','jamais','détesté'
    ];

    private array $topicsMap = [
        'delivery' => ['livraison','livrer','retard','colis','transport'],
        'price'    => ['prix','cher','chère','coût','promo','promotion'],
        'quality'  => ['qualité','solide','cassé','fragile','défaut'],
        'support'  => ['support','service client','sav','réponse','remboursement'],
        'speed'    => ['rapide','lent','vitesse','immédiat'],
        'packaging'=> ['emballage','packaging','carton'],
    ];

    public function analyze(string $text): array
    {
        $t = mb_strtolower($text);

        $pos = $this->countHits($t, $this->positive);
        $neg = $this->countHits($t, $this->negative);

        $sentiment = 'neutral';
        if ($pos > $neg) $sentiment = 'positive';
        if ($neg > $pos) $sentiment = 'negative';

        // Score 0–100 (base 50 + influence sentiment + intensité)
        $base = 50 + (($pos - $neg) * 12);
        $exclaim = substr_count($text, '!');
        $caps = preg_match_all('/\p{Lu}/u', $text);
        $lenBonus = min(10, (int)(mb_strlen($text) / 80));

        $score = (int) round($base + $exclaim * 2 + $caps * 0.2 + $lenBonus);
        $score = max(0, min(100, $score));

        $topics = $this->extractTopics($t);

        return [
            'sentiment' => $sentiment,
            'score' => $score,
            'topics' => $topics,
        ];
    }

    private function countHits(string $t, array $words): int
    {
        $count = 0;
        foreach ($words as $w) {
            if (str_contains($t, $w)) $count++;
        }
        return $count;
    }

    private function extractTopics(string $t): array
    {
        $topics = [];
        foreach ($this->topicsMap as $topic => $keywords) {
            foreach ($keywords as $k) {
                if (str_contains($t, $k)) {
                    $topics[] = $topic;
                    break;
                }
            }
        }
        return array_values(array_unique($topics));
    }
}
