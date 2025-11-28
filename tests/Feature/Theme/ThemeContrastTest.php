<?php

declare(strict_types=1);

dataset('theme-contrast', [
    'catppuccin-mocha' => [['foreground' => '#cdd6f4', 'background' => '#1e1e2e', 'accent' => '#cba6f7']],
    'catppuccin-latte' => [['foreground' => '#4c4f69', 'background' => '#eff1f5', 'accent' => '#8839ef']],
    'catppuccin-frappe' => [['foreground' => '#c6d0f5', 'background' => '#292c3c', 'accent' => '#ca9ee6']],
    'catppuccin-macchiato' => [['foreground' => '#cad3f5', 'background' => '#24273a', 'accent' => '#c6a0f6']],
    'tokyo-night-night' => [['foreground' => '#c0caf5', 'background' => '#24283b', 'accent' => '#7aa2f7']],
    'tokyo-night-day' => [['foreground' => '#1a1b26', 'background' => '#e1e2e7', 'accent' => '#3760bf']],
    'kanagawa-wave' => [['foreground' => '#dcd7ba', 'background' => '#1f1f28', 'accent' => '#7e9cd8']],
    'kanagawa-dragon' => [['foreground' => '#c5c9c5', 'background' => '#181616', 'accent' => '#8ba4b0']],
    'kanagawa-lotus' => [['foreground' => '#1f1f28', 'background' => '#f2ecbc', 'accent' => '#5d57a3']],
    'dracula' => [['foreground' => '#f8f8f2', 'background' => '#282a36', 'accent' => '#bd93f9']],
    'nord' => [['foreground' => '#eceff4', 'background' => '#2e3440', 'accent' => '#88c0d0']],
    'rose-pine' => [['foreground' => '#e0def4', 'background' => '#26233a', 'accent' => '#eb6f92']],
    'one-dark-pro' => [['foreground' => '#d7dae0', 'background' => '#292f3a', 'accent' => '#c678dd']],
    'monokai-pro' => [['foreground' => '#f9f8f5', 'background' => '#2a2418', 'accent' => '#fc9867']],
    'gruvbox-dark' => [['foreground' => '#ebdbb2', 'background' => '#1d2021', 'accent' => '#fabd2f']],
    'gruvbox-light' => [['foreground' => '#1d2021', 'background' => '#fbf1c7', 'accent' => '#b57614']],
    'solarized-dark' => [['foreground' => '#fdf6e3', 'background' => '#2f3a40', 'accent' => '#268bd2']],
    'solarized-light' => [['foreground' => '#1c1a15', 'background' => '#fdf6e3', 'accent' => '#268bd2']],
    'gov-uk' => [['foreground' => '#0b0c0c', 'background' => '#f3f2f1', 'accent' => '#1d70b8']],
    'transport-for-london' => [['foreground' => '#0d1430', 'background' => '#f5f7ff', 'accent' => '#0019a8']],
    'nhs-digital' => [['foreground' => '#003087', 'background' => '#f0f4f5', 'accent' => '#005eb8']],
    'financial-times' => [['foreground' => '#1a1716', 'background' => '#fff1e5', 'accent' => '#990f3d']],
    'the-guardian' => [['foreground' => '#052962', 'background' => '#f6f6f6', 'accent' => '#052962']],
]);

test('theme palettes satisfy wcag aa contrast targets', function (array $colors): void {
    expect(contrastRatio($colors['foreground'], $colors['background']))->toBeGreaterThanOrEqual(4.5);
    expect(contrastRatio($colors['accent'], $colors['background']))->toBeGreaterThanOrEqual(3.0);
})->with('theme-contrast');

function contrastRatio(string $hexA, string $hexB): float
{
    $lumA = relativeLuminance($hexA);
    $lumB = relativeLuminance($hexB);

    $light = max($lumA, $lumB);
    $dark = min($lumA, $lumB);

    return ($light + 0.05) / ($dark + 0.05);
}

function relativeLuminance(string $hex): float
{
    $hex = mb_ltrim($hex, '#');

    $r = hexdec(mb_substr($hex, 0, 2)) / 255;
    $g = hexdec(mb_substr($hex, 2, 2)) / 255;
    $b = hexdec(mb_substr($hex, 4, 2)) / 255;

    $channels = array_map(static fn (float $value): float => $value <= 0.03928
        ? $value / 12.92
        : (($value + 0.055) / 1.055) ** 2.4, [$r, $g, $b]);

    return 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];
}
