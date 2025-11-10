<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use SplFileInfo;

it('uses Bun as the JavaScript runtime across GitHub workflows', function (): void {
    $workflowPath = base_path('.github/workflows');
    expect(File::isDirectory($workflowPath))->toBeTrue();

    $workflows = collect(File::files($workflowPath))
        ->filter(fn (SplFileInfo $file): bool => $file->getExtension() === 'yml' || $file->getExtension() === 'yaml');

    expect($workflows)->not->toBeEmpty();

    $workflows->each(function (SplFileInfo $file): void {
        $contents = File::get($file->getPathname());

        expect($contents)
            ->toContain('oven-sh/setup-bun@v2')
            ->not->toContain('actions/setup-node')
            ->not->toContain('npm ');
    });

    expect(File::exists($workflowPath.'/nightly-heavy.yml'))->toBeTrue();
});
