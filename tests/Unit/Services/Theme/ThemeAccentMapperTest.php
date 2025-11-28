<?php

declare(strict_types=1);

use App\Enums\Theme;
use App\Enums\ThemeAccent;
use App\Enums\ThemeFlavor;
use App\Services\Theme\ThemeAccentMapper;

test('returns available accents for Catppuccin theme', function (): void {
    $mapper = new ThemeAccentMapper();
    $accents = $mapper->getAvailableAccents(Theme::Catppuccin);

    expect($accents)->toBeArray()
        ->toContain(ThemeAccent::Primary)
        ->toContain(ThemeAccent::Blue)
        ->toContain(ThemeAccent::Red)
        ->toContain(ThemeAccent::Green);
});

test('returns available accents for Kanagawa theme', function (): void {
    $mapper = new ThemeAccentMapper();
    $accents = $mapper->getAvailableAccents(Theme::Kanagawa);

    expect($accents)->toBeArray()
        ->toContain(ThemeAccent::Primary)
        ->toContain(ThemeAccent::Blue)
        ->toContain(ThemeAccent::Red)
        ->toContain(ThemeAccent::Green);
});

test('validates accent for theme correctly', function (): void {
    $mapper = new ThemeAccentMapper();

    expect($mapper->validateAccent(Theme::Catppuccin, ThemeAccent::Primary))->toBeTrue();
    expect($mapper->validateAccent(Theme::Catppuccin, ThemeAccent::Blue))->toBeTrue();
    expect($mapper->validateAccent(Theme::Kanagawa, ThemeAccent::Primary))->toBeTrue();
    expect($mapper->validateAccent(Theme::Kanagawa, ThemeAccent::Red))->toBeTrue();
});

test('generates Flux CSS variable name correctly', function (): void {
    $mapper = new ThemeAccentMapper();

    $variableName = $mapper->getFluxVariableName(
        Theme::Catppuccin,
        ThemeFlavor::Mocha,
        ThemeAccent::Primary
    );

    expect($variableName)->toBe('--accent-flux-zinc-500');
});

test('generates Filament CSS variable name correctly', function (): void {
    $mapper = new ThemeAccentMapper();

    $variableName = $mapper->getFilamentVariableName(
        Theme::Kanagawa,
        ThemeFlavor::Wave,
        ThemeAccent::Blue
    );

    expect($variableName)->toBe('--accent-filament-gray-500');
});

test('generates different variable names for different accents', function (): void {
    $mapper = new ThemeAccentMapper();

    $primary = $mapper->getFluxVariableName(Theme::Catppuccin, ThemeFlavor::Mocha, ThemeAccent::Primary);
    $blue = $mapper->getFluxVariableName(Theme::Catppuccin, ThemeFlavor::Mocha, ThemeAccent::Blue);

    expect($primary)->toBe('--accent-flux-zinc-500');
    expect($blue)->toBe('--accent-flux-zinc-500');
});
