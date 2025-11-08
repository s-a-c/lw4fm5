<?php

declare(strict_types=1);

/**
 * Compliant with [.ai/AI-GUIDELINES.md](.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e
 */

return [
    'schedules' => [
        'nightly' => env('BASE_PLATFORM_ENABLE_NIGHTLY', true),
        'weekly' => env('BASE_PLATFORM_ENABLE_WEEKLY', true),
        'monthly' => env('BASE_PLATFORM_ENABLE_MONTHLY', true),
    ],

    'profiles' => [
        'supported' => [
            'native',
            'container',
        ],
    ],

    'observability' => [
        'metrics_prefix' => env('BASE_PLATFORM_METRICS_PREFIX', 'base_platform'),
        'log_channel' => env('BASE_PLATFORM_LOG_CHANNEL', 'stack'),
    ],
];
