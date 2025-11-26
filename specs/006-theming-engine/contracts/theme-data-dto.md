# ThemeData DTO Contract

**Location**: `app/Data/ThemeData.php`
**Type**: Spatie Laravel Data DTO

## Purpose

Type-safe theme data for View Composer injection. Replaces array-based `themeData` with a strongly-typed Data object for better IDE support, type safety, and maintainability.

## Class Definition

```php
<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\Theme;
use App\Enums\ThemeAccent;
use App\Enums\ThemeFlavor;
use Spatie\LaravelData\Data;

final class ThemeData extends Data
{
    public function __construct(
        public Theme $theme,
        public ThemeFlavor $flavor,
        public ThemeAccent $accent,
    ) {}

    public function isLight(): bool
    {
        return $this->flavor->isLight();
    }
}
```

## Properties

### `theme: Theme`
- **Type**: `Theme` enum
- **Values**: `Theme::Catppuccin | Theme::Kanagawa`
- **Purpose**: Top-level theme selection

### `flavor: ThemeFlavor`
- **Type**: `ThemeFlavor` enum
- **Values**: Depends on selected theme
  - Catppuccin: `Latte`, `Frappe`, `Macchiato`, `Mocha`
  - Kanagawa: `Wave`, `Dragon`, `Lotus`
- **Purpose**: Theme variant/flavor

### `accent: ThemeAccent`
- **Type**: `ThemeAccent` enum
- **Values**: `Primary`, `Blue`, `Red`, `Green`
- **Purpose**: Accent color selection

## Methods

### `isLight(): bool`
- **Returns**: `true` if flavor is light (Latte, Lotus), `false` otherwise
- **Delegates**: Calls `$this->flavor->isLight()`
- **Usage**: Used in Blade templates to determine if `dark` class should be applied

## Usage in View Composer

```php
View::composer('*', function (View $view): void {
    $user = auth()->user();
    $settings = $user?->settings ?? new UserSettingsData();

    // Validate and correct if needed
    $theme = $settings->theme;
    $flavor = $settings->flavor;
    $accent = $settings->accent;

    // Validate theme/flavor combination
    $availableFlavors = $theme->flavors();
    if (!in_array($flavor, $availableFlavors)) {
        $flavor = ThemeFlavor::Mocha;
    }

    $view->with('themeData', new ThemeData(
        theme: $theme,
        flavor: $flavor,
        accent: $accent,
    ));
});
```

## Usage in Blade Templates

```blade
<html
    lang="en"
    data-theme="{{ $themeData->theme->value ?? 'catppuccin' }}"
    data-flavor="{{ $themeData->flavor->value ?? 'mocha' }}"
    data-accent="{{ $themeData->accent->value ?? 'primary' }}"
    @class(['dark' => !($themeData->isLight() ?? false)])
>
```

## Benefits Over Array

1. **Type Safety**: IDE autocomplete and type checking
2. **Refactoring**: Rename enum values with IDE support
3. **Documentation**: Self-documenting through class structure
4. **Validation**: Spatie Data provides built-in validation
5. **Consistency**: Matches existing pattern (`UserSettingsData`)

## Testing

```php
it('creates theme data with correct properties', function () {
    $themeData = new ThemeData(
        theme: Theme::Kanagawa,
        flavor: ThemeFlavor::Wave,
        accent: ThemeAccent::Blue,
    );

    expect($themeData->theme)->toBe(Theme::Kanagawa);
    expect($themeData->flavor)->toBe(ThemeFlavor::Wave);
    expect($themeData->accent)->toBe(ThemeAccent::Blue);
    expect($themeData->isLight())->toBeFalse();
});

it('correctly identifies light flavors', function () {
    $lightTheme = new ThemeData(
        theme: Theme::Catppuccin,
        flavor: ThemeFlavor::Latte,
        accent: ThemeAccent::Primary,
    );

    expect($lightTheme->isLight())->toBeTrue();
});
```
