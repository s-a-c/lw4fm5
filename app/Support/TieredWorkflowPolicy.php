<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;

final class TieredWorkflowPolicy
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public static function tiers(): array
    {
        /** @var array<string, array<string, mixed>> $tiers */
        $tiers = Config::get('base-platform.workflow_policy.tiers', []);

        return $tiers;
    }

    /**
     * @return array<int, string>
     */
    public static function workflowsFor(string $tier): array
    {
        $workflows = Arr::get(self::tiers(), "{$tier}.workflows", []);

        return is_array($workflows) ? array_values(array_filter($workflows, is_string(...))) : [];
    }
}
