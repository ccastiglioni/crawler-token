<?php

return [
    'health' => [
        'queue' => 'maintenance',
        'scheduler_key' => 'watchdog:health:scheduler',
        'worker_key' => 'watchdog:health:worker',
        'max_age_seconds' => (int) env('WATCHDOG_HEALTH_MAX_AGE_SECONDS', 180),
        'retention_seconds' => 3600,
    ],

    // Identificadores e aliases são dados. O núcleo não deve conhecer RVN.
    // Estes pares são candidatos configurados, não prova de listagem atual.
    'assets' => [
        'ravencoin' => [
            'enabled' => (bool) env('WATCHDOG_RVN_ENABLED', true),
            'symbol' => 'RVN',
            'name' => 'Ravencoin',
            'aliases' => ['RVN', 'Ravencoin'],
            'markets' => [
                'binance' => [
                    'spot' => ['RVNUSDT'],
                    'perpetual' => ['RVNUSDT'],
                ],
                'mexc' => [
                    'spot' => ['RVNUSDT'],
                    'perpetual' => ['RVN_USDT'],
                ],
            ],
        ],
    ],
];
