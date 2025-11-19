<?php

declare(strict_types=1);

namespace App\Services\BasePlatform;

use App\Models\WorkflowSuite;
use App\Models\WorkflowSuiteChannel;
use Illuminate\Support\Facades\Config;

final class WorkflowSuiteChannelSync
{
    public function sync(): void
    {
        /** @var array<string, array<int, array{channel: string, medium: string}>> $desired */
        $desired = Config::get('base-platform.workflow_suite_channels', []);
        $keep = [];

        foreach ($desired as $suiteName => $channels) {
            $suite = WorkflowSuite::query()->where('name', $suiteName)->first();

            if ($suite === null) {
                continue;
            }

            foreach ($channels as $channel) {
                $record = WorkflowSuiteChannel::query()->updateOrCreate(
                    [
                        'workflow_suite_id' => $suite->id,
                        'channel' => $channel['channel'],
                        'medium' => $channel['medium'],
                    ],
                    []
                );

                $keep[] = $record->id;
            }
        }

        if ($keep !== []) {
            /** @phpstan-ignore-next-line */
            WorkflowSuiteChannel::query()
                ->whereNotIn('id', $keep)
                ->delete();
        }
    }
}
