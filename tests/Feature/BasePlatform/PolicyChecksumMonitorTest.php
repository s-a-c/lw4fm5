<?php

declare(strict_types=1);

use function Pest\Laravel\artisan;

it('reports policy acknowledgement checksum status', function (): void {
    artisan('policy:checksum-monitor')
        ->expectsOutputToContain('Policy acknowledgement checksum summary')
        ->assertExitCode(0);
});
