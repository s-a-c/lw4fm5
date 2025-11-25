<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

it('uses Bun as the JavaScript runtime across GitHub workflows', function (): void {
    $workflowPath = base_path('.github/workflows');
    expect(File::isDirectory($workflowPath))->toBeTrue();

    $workflows = collect(File::files($workflowPath))
        ->filter(fn (SplFileInfo $file): bool => $file->getExtension() === 'yml' || $file->getExtension() === 'yaml');

    assert($workflows->isNotEmpty());

    $workflows->each(function (SplFileInfo $file): void {
        $contents = File::get($file->getPathname());

        assert(str_contains($contents, 'oven-sh/setup-bun@v2'));
        assert(! str_contains($contents, 'actions/setup-node'));
        assert(! str_contains($contents, 'npm '));
    });

    expect(File::exists($workflowPath.'/nightly-heavy.yml'))->toBeTrue();
});
