<?php

declare(strict_types=1);

use App\Services\BasePlatform\ComposerAuditRunner;
use Illuminate\Support\Facades\Process;

it('runs composer audit command and returns process result', function (): void {
    Process::fake([
        'composer audit --format=json' => Process::result(
            output: '{"advisories":[]}',
            errorOutput: '',
            exitCode: 0,
        ),
    ]);

    $runner = new ComposerAuditRunner();
    $result = $runner->run();

    expect($result->successful())->toBeTrue();
    expect(mb_trim($result->output()))->toBe('{"advisories":[]}');

    Process::assertRan('composer audit --format=json');
});
