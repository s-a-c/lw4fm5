<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

it('defines a nightly heavy-suite workflow with scheduled triggers', function (): void {
    $workflowFile = base_path('.github/workflows/nightly-heavy.yml');

    expect(File::exists($workflowFile))->toBeTrue();

    $contents = File::get($workflowFile);

    expect($contents)
        ->toContain('schedule:')
        ->toContain('cron:')
        ->toContain('nightly heavy-suite');
});
