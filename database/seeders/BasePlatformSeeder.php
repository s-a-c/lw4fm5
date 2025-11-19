<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CredentialPolicy;
use App\Models\EnvironmentProfile;
use App\Models\ToolchainDefinition;
use App\Models\WorkflowSuite;
use App\Models\WorkflowSuiteChannel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

final class BasePlatformSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedEnvironmentProfiles();
        $this->seedToolchainDefinitions();
        $this->seedCredentialPolicies();
        $this->seedWorkflowSuites();
        $this->seedWorkflowSuiteChannels();
    }

    private function seedEnvironmentProfiles(): void
    {
        $profiles = [
            [
                'id' => (string) Str::uuid(),
                'name' => 'native',
                'runtime_versions' => json_encode([
                    'php' => '8.5',
                    'bun' => '1.1',
                    'redis' => '7',
                    'postgres' => '15',
                ]),
                'prerequisites' => json_encode([
                    'herd' => true,
                    'docker' => false,
                ]),
                'smoke_check_script' => 'scripts/platform/bootstrap.sh',
                'status' => 'supported',
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'container',
                'runtime_versions' => json_encode([
                    'php' => '8.5',
                    'bun' => '1.1',
                    'redis' => '7',
                    'postgres' => '15',
                ]),
                'prerequisites' => json_encode([
                    'docker' => true,
                ]),
                'smoke_check_script' => 'scripts/platform/bootstrap.sh',
                'status' => 'supported',
            ],
        ];

        EnvironmentProfile::query()->upsert(
            $this->appendTimestamps($profiles),
            uniqueBy: ['name'],
            update: ['runtime_versions', 'prerequisites', 'smoke_check_script', 'status', 'updated_at']
        );
    }

    private function seedToolchainDefinitions(): void
    {
        $definitions = [
            [
                'id' => (string) Str::uuid(),
                'language' => 'php',
                'version' => '^8.5',
                'enforcement_scope' => 'both',
                'verification_command' => 'php -v',
                'documentation_url' => 'docs/base-platform/toolchain-baseline.md',
            ],
            [
                'id' => (string) Str::uuid(),
                'language' => 'javascript',
                'version' => '^1.1',
                'enforcement_scope' => 'both',
                'verification_command' => 'bun --version',
                'documentation_url' => 'docs/base-platform/toolchain-baseline.md',
            ],
        ];

        ToolchainDefinition::query()->upsert(
            $this->appendTimestamps($definitions),
            uniqueBy: ['language', 'enforcement_scope'],
            update: ['version', 'verification_command', 'documentation_url', 'updated_at']
        );
    }

    private function seedCredentialPolicies(): void
    {
        $policies = [
            [
                'id' => (string) Str::uuid(),
                'context' => 'ci',
                'storage_mechanism' => 'github_actions_secret',
                'rotation_interval_days' => 90,
                'owner' => 'Platform Engineering',
                'notes' => 'Rotate via GitHub Actions secret manager.',
            ],
            [
                'id' => (string) Str::uuid(),
                'context' => 'local',
                'storage_mechanism' => 'encrypted_env_file',
                'rotation_interval_days' => 120,
                'owner' => 'Solo Developer',
                'notes' => 'Store in 1Password and `.env.native`.',
            ],
        ];

        CredentialPolicy::query()->upsert(
            $this->appendTimestamps($policies),
            uniqueBy: ['context', 'storage_mechanism'],
            update: ['rotation_interval_days', 'owner', 'notes', 'updated_at']
        );
    }

    private function seedWorkflowSuites(): void
    {
        $suites = [
            [
                'id' => (string) Str::uuid(),
                'name' => 'core-quality',
                'triggers' => json_encode(['push', 'pull_request']),
                'required_checks' => json_encode(['lint', 'test', 'type']),
                'sla_minutes' => 25,
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'heavy-quality',
                'triggers' => json_encode(['nightly', 'release']),
                'required_checks' => json_encode(['mutation', 'browser']),
                'sla_minutes' => 120,
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'dependency-governance',
                'triggers' => json_encode(['monthly']),
                'required_checks' => json_encode(['dependency-review', 'dependency-review-performance']),
                'sla_minutes' => 30,
            ],
        ];

        WorkflowSuite::query()->upsert(
            $this->appendTimestamps($suites),
            uniqueBy: ['name'],
            update: ['triggers', 'required_checks', 'sla_minutes', 'updated_at']
        );
    }

    private function seedWorkflowSuiteChannels(): void
    {
        $desiredRaw = Config::get('base-platform.workflow_suite_channels', []);
        $desired = is_array($desiredRaw) ? $desiredRaw : [];

        $records = [];

        foreach ($desired as $suiteName => $channels) {
            if (! is_string($suiteName)) {
                continue;
            }
            if (! is_array($channels)) {
                continue;
            }
            $suite = WorkflowSuite::query()->where('name', $suiteName)->first();

            if ($suite === null) {
                continue;
            }

            foreach ($channels as $channel) {
                if (! is_array($channel)) {
                    continue;
                }
                if (! isset($channel['channel'], $channel['medium'])) {
                    continue;
                }
                $records[] = [
                    'id' => (string) Str::uuid(),
                    'workflow_suite_id' => $suite->id,
                    'channel' => is_string($channel['channel']) ? $channel['channel'] : '',
                    'medium' => is_string($channel['medium']) ? $channel['medium'] : '',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if ($records !== []) {
            WorkflowSuiteChannel::query()->upsert(
                $records,
                uniqueBy: ['workflow_suite_id', 'channel', 'medium'],
                update: ['updated_at']
            );
        }
    }

    /**
     * @param  list<array<string, mixed>>  $records
     * @return list<array<string, mixed>>
     */
    private function appendTimestamps(array $records): array
    {
        $now = now();

        return array_map(static fn (array $attributes): array => $attributes + [
            'created_at' => $now,
            'updated_at' => $now,
        ], $records);
    }
}
