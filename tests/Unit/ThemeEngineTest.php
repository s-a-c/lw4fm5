<?php

declare(strict_types=1);

use App\Data\UserSettingsData;
use App\Enums\Theme;
use App\Enums\ThemeFlavor;

test('theme returns correct flavors', function (): void {
    $flavors = Theme::Catppuccin->flavors();
    expect($flavors)->toContain(ThemeFlavor::Mocha);
    expect($flavors)->not->toContain(ThemeFlavor::Wave);
});

test('flavor identifies light mode', function (): void {
    expect(ThemeFlavor::Latte->isLight())->toBeTrue()
        ->and(ThemeFlavor::Mocha->isLight())->toBeFalse();
});

test('settings dto defaults to mocha', function (): void {
    $data = new UserSettingsData();
    expect($data->theme)->toBe(Theme::Catppuccin)
        ->and($data->flavor)->toBe(ThemeFlavor::Mocha);
});
