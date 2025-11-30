<?php

declare(strict_types=1);

use App\Data\UserSettingsData;
use App\Enums\Theme;
use App\Enums\ThemeAccent;
use App\Support\ThemeColorHelper;
use Filament\Support\Colors\Color;

foreach ([
    'catppuccin' => [Theme::Catppuccin, '#cba6f7'],
    'kanagawa' => [Theme::Kanagawa, '#7e9cd8'],
] as $label => [$theme, $expectedHex]) {
    test("primary accent uses the theme brand color for {$label}", function () use ($theme, $expectedHex): void {
        $colors = ThemeColorHelper::getFilamentColors(new UserSettingsData(
            theme: $theme,
            accent: ThemeAccent::Primary,
        ));

        expect($colors)
            ->toHaveKey('primary')
            ->and($colors['primary'][500])->toBe(Color::hex($expectedHex)[500]);
    });
}

$accentCases = [
    'catppuccin secondary' => [Theme::Catppuccin, ThemeAccent::Secondary, '#89b4fa'],
    'catppuccin error' => [Theme::Catppuccin, ThemeAccent::Error, '#f38ba8'],
    'catppuccin success' => [Theme::Catppuccin, ThemeAccent::Success, '#a6e3a1'],
    'kanagawa secondary' => [Theme::Kanagawa, ThemeAccent::Secondary, '#7fb4ca'],
    'kanagawa error' => [Theme::Kanagawa, ThemeAccent::Error, '#c34043'],
    'kanagawa success' => [Theme::Kanagawa, ThemeAccent::Success, '#76946a'],
];

foreach ($accentCases as $label => [$theme, $accent, $expectedHex]) {
    test("non-primary accent {$label} resolves to the correct palette", function () use ($theme, $accent, $expectedHex): void {
        $colors = ThemeColorHelper::getFilamentColors(new UserSettingsData(
            theme: $theme,
            accent: $accent,
        ));

        expect($colors['primary'][500])->toBe(Color::hex($expectedHex)[500]);
    });
}
