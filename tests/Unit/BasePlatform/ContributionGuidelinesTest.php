<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

it('documents the contribution checklist and dependency governance workflow', function (): void {
    $path = base_path('docs/base-platform/contribution-guidelines.md');

    expect(File::exists($path))->toBeTrue();

    $contents = File::get($path);

    expect($contents)->toContain('Compliant with [.ai/AI-GUIDELINES.md]');
    expect($contents)->toContain('## Required Pull Request Checklist');
    expect($contents)->toContain('- [ ] composer workflow:core');
    expect($contents)->toContain('- [ ] php artisan platform:dependency-review');
    expect($contents)->toContain('- [ ] php artisan platform:dependency-review-performance-report');
    expect($contents)->toContain('## QA Evidence & Links');
    expect($contents)->toContain('storage/app/base-platform/dependency-reports/');
    expect($contents)->toContain('docs/base-platform/support-metrics.md');
});
