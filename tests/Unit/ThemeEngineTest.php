<?php

declare(strict_types=1);

use App\Enums\Theme;
use App\Enums\ThemeFlavor;
use App\Data\UserSettingsData;

test('theme returns correct flavors', function () {
    expect(Theme::Catppuccin->flavors())
        ->toContain(ThemeFlavor::Mocha)
        ->not->toContain(ThemeFlavor::Wave);
});

test('flavor identifies light mode', function () {
    expect(ThemeFlavor::Latte->isLight())->toBeTrue()
        ->and(ThemeFlavor::Mocha->isLight())->toBeFalse();
});

test('settings dto defaults to mocha', function () {
    $data = new UserSettingsData();
    expect($data->theme)->toBe(Theme::Catppuccin)
        ->and($data->flavor)->toBe(ThemeFlavor::Mocha);
});
