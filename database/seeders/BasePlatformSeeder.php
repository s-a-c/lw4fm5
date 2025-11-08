<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CredentialPolicy;
use App\Models\EnvironmentProfile;
use App\Models\ToolchainDefinition;
use App\Models\WorkflowSuite;
use App\Models\WorkflowSuiteChannel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
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
                'runtime_versions' => [
                    'php' => '8.5',
                    'bun' => '1.1',
                    'redis' => '7',
                    'postgres' => '15',
                ],
                'prerequisites' => [
                    'herd' => true,
                    'docker' => false,
                ],
                'smoke_check_script' => 'scripts/platform/bootstrap.sh',
                'status' => 'supported',
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'container',
                'runtime_versions' => [
                    'php' => '8.5',
                    'bun' => '1.1',
                    'redis' => '7',
                    'postgres' => '15',
                ],
                'prerequisites' => [
                    'docker' => true,
                ],
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
                'triggers' => ['push', 'pull_request'],
                'required_checks' => ['lint', 'test', 'type'],
                'sla_minutes' => 25,
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'heavy-quality',
                'triggers' => ['nightly', 'release'],
                'required_checks' => ['mutation', 'browser'],
                'sla_minutes' => 120,
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
        $desired = Config::get('base-platform.workflow_suite_channels', []);

        $records = [];

        foreach ($desired as $suiteName => $channels) {
            $suite = WorkflowSuite::query()->where('name', $suiteName)->first();

            if ($suite === null) {
                continue;
            }

            foreach ($channels as $channel) {
                $records[] = [
                    'id' => (string) Str::uuid(),
                    'workflow_suite_id' => $suite->id,
                    'channel' => $channel['channel'],
                    'medium' => $channel['medium'],
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

        return array_map(static function (array $attributes) use ($now): array {
            return Arr::only($attributes + [
                'created_at' => $now,
                'updated_at' => $now,
            ], array_keys($attributes) + ['created_at', 'updated_at']);
        }, $records);
    }
}
