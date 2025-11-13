<?php

declare(strict_types=1);

/**
 * Compliant with [.ai/AI-GUIDELINES.md](../../.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e
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

    'workflow_policy' => [
        'tiers' => [
            'core' => [
                'workflows' => [
                    'tests.yml',
                    'lint.yml',
                ],
                'checks' => [
                    'lint',
                    'test',
                    'type',
                ],
                'sla_minutes' => 25,
            ],
            'heavy' => [
                'workflows' => [
                    'nightly-heavy.yml',
                    'browser-tests.yml',
                ],
                'checks' => [
                    'mutation',
                    'browser',
                ],
                'sla_minutes' => 120,
            ],
        ],
    ],

    'workflow_suite_channels' => [
        'core-quality' => [
            [
                'channel' => 'slack::#ci-core-quality',
                'medium' => 'slack',
            ],
        ],
        'heavy-quality' => [
            [
                'channel' => 'slack::#ci-heavy-quality',
                'medium' => 'slack',
            ],
            [
                'channel' => 'email::platform-alerts@example.com',
                'medium' => 'email',
            ],
        ],
        'dependency-governance' => [
            [
                'channel' => 'slack::#dependency-governance',
                'medium' => 'slack',
            ],
            [
                'channel' => 'email::platform-governance@example.com',
                'medium' => 'email',
            ],
        ],
    ],

    'policy' => [
        'acknowledgement_checksum' => env('BASE_PLATFORM_POLICY_CHECKSUM', '3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e'),
        'files' => [
            'specs/001-base-platform/spec.md',
            'specs/001-base-platform/plan.md',
            'specs/001-base-platform/data-model.md',
            'specs/001-base-platform/research.md',
            'specs/001-base-platform/quickstart.md',
            'specs/001-base-platform/tasks.md',
            'specs/001-base-platform/checklists/architecture.md',
            'specs/001-base-platform/checklists/implementation.md',
            'specs/001-base-platform/checklists/quality-assurance.md',
            'docs/base-platform/bootstrap-recovery.md',
            'docs/base-platform/credential-onboarding.md',
            'docs/base-platform/credential-rotation.md',
            'docs/base-platform/environment-validation.md',
            'docs/base-platform/offline-proxy.md',
            'docs/base-platform/ci-policy.md',
            'docs/base-platform/quickstart.md',
        ],
    ],
];
