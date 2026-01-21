<?php

namespace App\Services\AI;

class AnalyzerManager
{
    public function driver(): AnalyzerInterface
    {
        return match (config('ai.provider')) {
            'external' => new ExternalApiAnalyzer(),
            default => new RuleBasedAnalyzer(),
        };
    }
}
