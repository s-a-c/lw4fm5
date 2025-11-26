# Data Model: Theming Engine

**Date**: 2025-11-25
**Feature**: Theming Engine Implementation

## Overview

The Theming Engine uses existing Laravel Eloquent models and Spatie Data objects. No new database tables are required. Theme preferences are stored in the existing `users` table's `settings` JSON column.

## Entities

### User

**Table**: `users`
**Model**: `App\Models\User`

**Fields**:

- `id` (bigint, primary key)
- `settings` (json, nullable) - Stores `UserSettingsData` DTO

**Relationships**: None (theme settings are user-specific)

**Casts**:

```php
'settings' => UserSettingsData::class

```

**Behavior**:

- On model retrieval (`booted()` method): If `settings` is null, initializes with default `UserSettingsData`
- Settings are automatically serialized/deserialized via Spatie Laravel Data

### UserSettingsData

**Type**: Spatie Data DTO
**Class**: `App\Data\UserSettingsData`

**Fields**:

- `theme` (Theme enum, default: `Theme::Catppuccin`)
- `flavor` (ThemeFlavor enum, default: `ThemeFlavor::Mocha`)
- `accent` (ThemeAccent enum, default: `ThemeAccent::Primary`)

### ThemeData

**Type**: Spatie Data DTO
**Class**: `App\Data\ThemeData`

**Purpose**: Type-safe theme data for View Composer injection (replaces array)

**Fields**:

- `theme` (Theme enum)
- `flavor` (ThemeFlavor enum)
- `accent` (ThemeAccent enum)

**Methods**:

- `isLight(): bool` - Delegates to `$this->flavor->isLight()`

**Validation Rules**:

- `theme` must be a valid `Theme` enum case
- `flavor` must be a valid `ThemeFlavor` enum case
- `accent` must be a valid `ThemeAccent` enum case
- `flavor` must belong to the selected `theme` (e.g., `Latte` belongs to `Catppuccin`, not `Kanagawa`)

**Default Values**:

- Theme: `Catppuccin`
- Flavor: `Mocha`
- Accent: `Primary`

### Theme

**Type**: PHP Enum
**Class**: `App\Enums\Theme`

**Values**:

- `Catppuccin` (value: `'catppuccin'`)
- `Kanagawa` (value: `'kanagawa'`)

**Methods**:

- `label(): string` - Human-readable label
- `flavors(): array<ThemeFlavor>` - Returns available flavors for this theme

**Relationships**:

- One Theme has many ThemeFlavors (via `flavors()` method)

### ThemeFlavor

**Type**: PHP Enum
**Class**: `App\Enums\ThemeFlavor`

**Values**:

- Catppuccin flavors: `Latte`, `Frappe`, `Macchiato`, `Mocha`
- Kanagawa flavors: `Wave`, `Dragon`, `Lotus`

**Methods**:

- `label(): string` - Human-readable label
- `isLight(): bool` - Returns true for light flavors (Latte, Lotus)

**Relationships**:

- Many ThemeFlavors belong to one Theme (inverse of Theme::flavors())

**Validation**:

- `Latte`, `Frappe`, `Macchiato`, `Mocha` belong to `Theme::Catppuccin`
- `Wave`, `Dragon`, `Lotus` belong to `Theme::Kanagawa`

### ThemeAccent

**Type**: PHP Enum
**Class**: `App\Enums\ThemeAccent`

**Values**:

- `Primary` (value: `'primary'`)
- `Blue` (value: `'blue'`)
- `Red` (value: `'red'`)
- `Green` (value: `'green'`)

**Methods**:

- `label(): string` - Human-readable label (capitalized value)

**Relationships**: None (accent is independent of theme/flavor)

## State Transitions

### User Settings Lifecycle

1. **Initial State** (New User):
   - `settings` column is `null`
   - On model retrieval: `booted()` initializes with default `UserSettingsData`
   - Default values: `Theme::Catppuccin`, `ThemeFlavor::Mocha`, `ThemeAccent::Primary`

2. **Theme Selection** (Authenticated User):
   - User selects theme/flavor/accent in appearance settings
   - Livewire component updates property via `wire:model.live`
   - `updated()` method triggers:
     - Validates theme/flavor combination
     - Updates `UserSettingsData` DTO
     - Saves to database via `$user->save()`
     - Updates DOM via `$this->js()` for live preview

3. **Invalid State** (Corrupted Data):
   - Database contains invalid enum values or invalid theme/flavor combination
   - On model retrieval: Validation occurs (in `booted()` or `ThemeService`)
   - Invalid settings are reset to defaults
   - Corrected settings are persisted to database silently

4. **Page Load** (Any User):
   - Server-side: View Composer reads user settings (or defaults for unauthenticated)
   - Validates theme/flavor/accent combination
   - Creates `ThemeData` DTO instance
   - Injects `ThemeData` DTO into view as `themeData`
   - Layout template applies attributes to `<html>` element using `$themeData->theme->value`, etc.
   - Client-side: `resources/js/app.js` ensures attributes are set (fallback)

### Theme Preview Page State (Unauthenticated Visitor)

1. **Initial Load**:
   - No user settings available
   - Reads from `sessionStorage` (if exists from previous visit to theme preview page)
   - Applies theme from sessionStorage or defaults to `Theme::Catppuccin` with `ThemeFlavor::Mocha` and `ThemeAccent::Primary`

2. **Theme Change**:
   - User selects theme/flavor/accent
   - JavaScript updates `sessionStorage` immediately
   - JavaScript updates DOM attributes immediately
   - No database writes

3. **Navigation Away**:
   - SessionStorage persists (browser behavior)
   - Other pages ignore sessionStorage, use user settings or defaults
   - Theme changes are effectively "reset" for other pages

## Validation Rules

### Theme/Flavor Combination Validation

**Rule**: A flavor must belong to its theme.

**Valid Combinations**:

- `Catppuccin` + `Latte` ✅
- `Catppuccin` + `Frappe` ✅
- `Catppuccin` + `Macchiato` ✅
- `Catppuccin` + `Mocha` ✅
- `Kanagawa` + `Wave` ✅
- `Kanagawa` + `Dragon` ✅
- `Kanagawa` + `Lotus` ✅

**Invalid Combinations**:

- `Catppuccin` + `Wave` ❌ (Wave belongs to Kanagawa)
- `Kanagawa` + `Latte` ❌ (Latte belongs to Catppuccin)

**Validation Logic**:

```php
$theme = Theme::from($settings->theme->value);
$availableFlavors = $theme->flavors();
if (!in_array($settings->flavor, $availableFlavors)) {
    // Invalid: reset to default
    $settings->flavor = ThemeFlavor::Mocha;
}

```

### Enum Value Validation

**Rule**: All enum values must be valid enum cases.

**Validation**:

- `Theme::tryFrom($value)` - Returns null if invalid
- `ThemeFlavor::tryFrom($value)` - Returns null if invalid
- `ThemeAccent::tryFrom($value)` - Returns null if invalid

**On Invalid**: Reset to defaults (`Catppuccin`, `Mocha`, `Primary`)

## Data Flow

### Server-Side Theme Injection Flow

```log
1. Request arrives
   ↓
2. Laravel loads User model (if authenticated)
   ↓
3. User model booted() initializes settings if null
   ↓
4. View Composer (AppServiceProvider) reads user->settings
   ↓
5. ThemeService validates theme/flavor/accent combination
   ↓
6. View Composer injects data-theme, data-flavor, data-accent into view
   ↓
7. Layout template applies attributes to <html> element
   ↓
8. CSS attribute selectors apply theme colors

```

### Client-Side Live Update Flow

```log
1. User selects theme/flavor/accent in UI
   ↓
2. Livewire wire:model.live triggers updated() method
   ↓
3. updated() validates theme/flavor combination
   ↓
4. updated() saves to database via $user->save()
   ↓
5. updated() calls $this->js() to update DOM attributes
   ↓
6. CSS attribute selectors apply new theme colors
   ↓
7. Visual update complete (<200ms)

```

### Theme Preview Page Flow (Unauthenticated)

```log
1. Visitor loads theme preview page
   ↓
2. JavaScript reads sessionStorage for theme preferences
   ↓
3. If sessionStorage exists: apply theme
   If not: use defaults (Catppuccin Mocha)
   ↓
4. User selects new theme
   ↓
5. JavaScript updates sessionStorage
   ↓
6. JavaScript updates DOM attributes
   ↓
7. CSS applies new theme
   ↓
8. User navigates away
   ↓
9. Other pages ignore sessionStorage, use defaults

```

## Database Schema

No schema changes required. Uses existing `users.settings` JSON column.

**Example JSON Structure**:
```json
{
  "theme": "catppuccin",
  "flavor": "mocha",
  "accent": "primary"
}
```

## Migration Requirements

None. Feature uses existing database structure.
