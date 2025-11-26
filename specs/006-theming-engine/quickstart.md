# Quickstart: Theming Engine Implementation

**Date**: 2025-11-25
**Feature**: Theming Engine
**Branch**: `006-theming-engine`

## Overview

This guide provides a quick reference for implementing the Theming Engine feature. See [spec.md](./spec.md) for detailed requirements and [research.md](./research.md) for technical decisions.

## Implementation Checklist

### Phase 1: Server-Side Theme Injection

- [ ] **Create `ThemeData` DTO**
  - Create `app/Data/ThemeData.php` with `Theme`, `ThemeFlavor`, `ThemeAccent` properties
  - Add `isLight()` method that delegates to `$flavor->isLight()`

- [ ] **Modify `AppServiceProvider`**
  - Add View Composer in `boot()` method
  - Read user settings (or defaults for unauthenticated)
  - Validate theme/flavor/accent combination
  - Create `ThemeData` DTO instance
  - Inject `ThemeData` DTO into all views as `themeData`

- [ ] **Update Layout Templates**
  - Apply `data-theme`, `data-flavor`, `data-accent` to `<html>` element
  - Apply `dark` class based on `!$themeData->isLight()`
  - Ensure Filament panel layouts receive theme data

### Phase 2: Client-Side Enhancements

- [ ] **Enhance `resources/js/app.js`**
  - Ensure theme initialization on `DOMContentLoaded`
  - Handle missing data attributes (fallback to defaults)
  - Manage `dark` class based on flavor

- [ ] **Verify Livewire Component**
  - Confirm `appearance.blade.php` already implements live preview
  - Test auto-save functionality
  - Verify DOM updates via `$this->js()`

### Phase 3: Theme Validation

- [ ] **Create `ThemeService` (Optional)**
  - Centralize validation logic
  - Validate theme/flavor/accent combinations
  - Provide default resolution

- [ ] **Enhance User Model**
  - Add validation in `booted()` method (if not using service)
  - Ensure invalid settings are reset to defaults
  - Persist corrected settings silently

### Phase 4: Theme Preview Page

- [ ] **Rename Demo Page**
  - Source: `resources/views/pages/tailwindcss.catppuccin.com/index.blade.php`
  - Target: `resources/views/pages/themes/preview.blade.php`
  - Route: `/themes/preview` (Folio auto-generated from file path, no middleware)

- [ ] **Update Theme Preview Page**
  - Add theme selection controls for both Catppuccin and Kanagawa
  - Implement session storage for temporary theme changes
  - Ensure theme resets on navigation away

### Phase 5: Testing

- [ ] **Unit Tests**
  - `ThemeServiceTest`: Validation logic
  - Enum relationship tests

- [ ] **Feature Tests**
  - `ThemePersistenceTest`: Save and retrieve theme settings
  - `ThemeValidationTest`: Invalid combination handling
  - `ThemeGlobalApplicationTest`: Theme applies to all pages
  - `ThemePreviewPageTest`: Theme preview page functionality

- [ ] **Browser Tests**
  - Live preview functionality
  - Theme preview page behavior

## Key Code Patterns

### View Composer Pattern

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
        // Optionally persist correction
    }

    $view->with('themeData', new ThemeData(
        theme: $theme,
        flavor: $flavor,
        accent: $accent,
    ));
});

```

### Layout Template Pattern

```blade
<html
    lang="en"
    data-theme="{{ $themeData->theme->value ?? 'catppuccin' }}"
    data-flavor="{{ $themeData->flavor->value ?? 'mocha' }}"
    data-accent="{{ $themeData->accent->value ?? 'primary' }}"
    @class(['dark' => !($themeData->isLight() ?? false)])
>

```

### Livewire Live Update Pattern

```php
public function updated(string $property, mixed $value): void
{
    // Validate and save
    $user = auth()->user();
    $settings = $user->settings ?? new UserSettingsData();

    if ($property === 'theme') $settings->theme = Theme::from($value);
    if ($property === 'flavor') $settings->flavor = ThemeFlavor::from($value);
    if ($property === 'accent') $settings->accent = ThemeAccent::from($value);

    $user->settings = $settings;
    $user->save();

    // Live DOM update
    $this->js(<<<'JS'
        const r = document.documentElement;
        r.dataset.theme = $wire.theme;
        r.dataset.flavor = $wire.flavor;
        r.dataset.accent = $wire.accent;

        const lightFlavors = ['latte', 'lotus'];
        if (lightFlavors.includes($wire.flavor)) {
            r.classList.remove('dark');
        } else {
            r.classList.add('dark');
        }
    JS);
}

```

### Theme Preview Page Session Storage Pattern

```javascript
// On page load
const theme = sessionStorage.getItem('theme') || 'catppuccin';
const flavor = sessionStorage.getItem('flavor') || 'mocha';
const accent = sessionStorage.getItem('accent') || 'primary';

document.documentElement.dataset.theme = theme;
document.documentElement.dataset.flavor = flavor;
document.documentElement.dataset.accent = accent;

// On theme change
function updateTheme(newTheme, newFlavor, newAccent) {
    sessionStorage.setItem('theme', newTheme);
    sessionStorage.setItem('flavor', newFlavor);
    sessionStorage.setItem('accent', newAccent);

    document.documentElement.dataset.theme = newTheme;
    document.documentElement.dataset.flavor = newFlavor;
    document.documentElement.dataset.accent = newAccent;
}

```

## Testing Strategy

### Unit Tests

Test validation logic and enum relationships:

```php
it('validates theme and flavor combination', function () {
    $theme = Theme::Catppuccin;
    $flavor = ThemeFlavor::Mocha;

    expect($theme->flavors())->toContain($flavor);
});

it('rejects invalid theme and flavor combination', function () {
    $theme = Theme::Catppuccin;
    $flavor = ThemeFlavor::Wave; // Wave belongs to Kanagawa

    expect($theme->flavors())->not->toContain($flavor);
});

```

### Feature Tests

Test persistence and global application:

```php
it('persists theme settings to database', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->livewire('settings.appearance')
        ->set('theme', 'kanagawa')
        ->set('flavor', 'wave')
        ->set('accent', 'blue');

    $user->refresh();
    expect($user->settings->theme)->toBe(Theme::Kanagawa);
    expect($user->settings->flavor)->toBe(ThemeFlavor::Wave);
    expect($user->settings->accent)->toBe(ThemeAccent::Blue);
});

it('applies theme globally via view composer', function () {
    $user = User::factory()->create([
        'settings' => new UserSettingsData(
            theme: Theme::Kanagawa,
            flavor: ThemeFlavor::Dragon,
            accent: ThemeAccent::Red,
        ),
    ]);

    $this->actingAs($user)
        ->get('/')
        ->assertSee('data-theme="kanagawa"', false)
        ->assertSee('data-flavor="dragon"', false)
        ->assertSee('data-accent="red"', false);
});

```

### Browser Tests

Test live preview and theme preview page:

```php
it('updates theme with live preview', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->visit('/settings/appearance')
        ->select('theme', 'kanagawa')
        ->waitFor('[data-theme="kanagawa"]')
        ->assertSee('data-theme="kanagawa"', false);
});

it('allows theme switching on preview page', function () {
    $this->visit('/themes/preview')
        ->select('theme', 'kanagawa')
        ->waitFor('[data-theme="kanagawa"]')
        ->assertSee('data-theme="kanagawa"', false);
});
```

## Common Pitfalls

1. **FOUC (Flash of Unstyled Content)**
   - Ensure View Composer injects theme data before layout renders
   - Use inline styles or data attributes in `<html>` element

2. **Invalid Theme Combinations**
   - Always validate theme/flavor combination before saving
   - Auto-correct silently, don't show errors to user

3. **Dark Mode Class**
   - Light flavors (latte, lotus) should remove `dark` class
   - All other flavors should add `dark` class

4. **Theme Preview Page Theme Leakage**
   - Theme preview page should use session storage, not database
   - Other pages should ignore session storage

5. **Filament Panel Integration**
   - Ensure Filament layout templates receive theme data
   - Test theme application in Filament panels

## Success Criteria Verification

- [ ] **SC-001**: Users can change theme with immediate visual feedback
- [ ] **SC-002**: Theme changes visible in <200ms
- [ ] **SC-003**: All themes and flavors render correctly
- [ ] **SC-004**: Application loads with no/invalid settings
- [ ] **SC-005**: Unauthenticated visitors can access theme preview page
- [ ] **SC-006**: Theme preview page theme changes reset on navigation

## Next Steps

After implementation:

1. Run test suite: `php artisan test`
2. Verify in browser: Test theme switching, persistence, global application
3. Test theme preview page: Verify session storage behavior
4. Code review: Ensure Laravel/Livewire best practices
5. Deploy: Follow standard deployment process
