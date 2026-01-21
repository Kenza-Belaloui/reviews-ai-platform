<?php

namespace App\Services\AI;

interface AnalyzerInterface
{
    /** @return array{sentiment:string, score:int, topics:array<int,string>} */
    public function analyze(string $text): array;
}
