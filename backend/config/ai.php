<?php

return [
    'provider' => env('AI_PROVIDER', 'rule_based'), // rule_based | external

    'external' => [
        'url' => env('AI_EXTERNAL_URL', ''),
        'key' => env('AI_EXTERNAL_KEY', ''),
        'timeout' => (int) env('AI_EXTERNAL_TIMEOUT', 10),
    ],
];
