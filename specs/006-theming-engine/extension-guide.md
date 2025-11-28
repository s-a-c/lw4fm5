# Theme Extension Guide

**Task**: T028p [FR-048]
**Status**: Complete

## Overview

This document provides a guide for extending the theming engine, including enum extension procedures, CSS file updates, ThemeAccentMapper service updates, validation rule updates, and migration strategy for existing user data.

## Extension Points

### 1. Theme Enum Extension

**File**: `app/Enums/Theme.php`

**Procedure**:
1. Add new enum case:
   ```php
   case NewTheme = 'new-theme';
   ```

2. Add label method case:
   ```php
   public function label(): string
   {
       return match ($this) {
           // ... existing cases ...
           self::NewTheme => 'New Theme',
       };
   }
   ```

3. Add flavors method case:
   ```php
   public function flavors(): array
   {
       return match ($this) {
           // ... existing cases ...
           self::NewTheme => [
               ThemeFlavor::Flavor1,
               ThemeFlavor::Flavor2,
           ],
       };
   }
   ```

**Testing**: Update `ThemeServiceTest` to include new theme

### 2. ThemeFlavor Enum Extension

**File**: `app/Enums/ThemeFlavor.php`

**Procedure**:
1. Add new enum case:
   ```php
   case NewFlavor = 'new-flavor';
   ```

2. Add label method case:
   ```php
   public function label(): string
   {
       return match ($this) {
           // ... existing cases ...
           self::NewFlavor => 'New Flavor',
       };
   }
   ```

3. Add isLight method case:
   ```php
   public function isLight(): bool
   {
       return match ($this) {
           // ... existing cases ...
           self::NewFlavor => false, // or true for light
       };
   }
   ```

**Testing**: Update `ThemeServiceTest` to include new flavor

### 3. ThemeAccent Enum Extension

**File**: `app/Enums/ThemeAccent.php`

**Procedure**:
1. Add new enum case:
   ```php
   case NewAccent = 'new-accent';
   ```

2. Add label method case:
   ```php
   public function label(): string
   {
       return match ($this) {
           // ... existing cases ...
           self::NewAccent => 'New Accent',
       };
   }
   ```

**Testing**: Update `ThemeAccentMapperTest` to include new accent

### 4. CSS File Updates

**File**: `resources/css/themes/all-themes.css`

**Procedure**:
1. Add theme CSS variables:
   ```css
   [data-theme="new-theme"][data-flavor="flavor1"][data-accent="primary"] {
     --color-background: #ffffff;
     --color-foreground: #000000;
     /* ... other colors ... */
   }
   ```

2. Add flavor variants:
   ```css
   [data-theme="new-theme"][data-flavor="flavor2"][data-accent="primary"] {
     /* ... flavor2 colors ... */
   }
   ```

3. Add accent variants:
   ```css
   [data-theme="new-theme"][data-flavor="flavor1"][data-accent="new-accent"] {
     --accent-flux-zinc-500: #new-accent-color;
     --accent-filament-gray-500: #new-accent-color;
   }
   ```

**Testing**:
- Visual testing in browser
- Contrast testing (WCAG AA)
- All theme combinations

### 5. ThemeAccentMapper Service Updates

**File**: `app/Services/Theme/ThemeAccentMapper.php`

**Procedure**:
1. Update `getAvailableAccents()`:
   ```php
   public function getAvailableAccents(Theme $theme): array
   {
       return match ($theme) {
           // ... existing cases ...
           Theme::NewTheme => [
               ThemeAccent::Primary,
               ThemeAccent::Blue,
               ThemeAccent::NewAccent,
           ],
       };
   }
   ```

2. Update `validateAccent()`:
   ```php
   public function validateAccent(Theme $theme, ThemeAccent $accent): bool
   {
       return match ($theme) {
           // ... existing cases ...
           Theme::NewTheme => in_array($accent, [
               ThemeAccent::Primary,
               ThemeAccent::Blue,
               ThemeAccent::NewAccent,
           ]),
       };
   }
   ```

3. Update CSS variable name methods:
   ```php
   public function getFluxVariableName(Theme $theme, ThemeFlavor $flavor, ThemeAccent $accent): string
   {
       return match ([$theme, $accent]) {
           // ... existing cases ...
           [Theme::NewTheme, ThemeAccent::NewAccent] => '--accent-flux-zinc-500',
       };
   }
   ```

**Testing**: Update `ThemeAccentMapperTest` to include new theme/accent combinations

### 6. Validation Rule Updates

**File**: `app/Services/Theme/ThemeService.php`

**Procedure**:
1. Validation automatically handles new enums (via `tryFrom()`)
2. Theme/flavor combination validation uses `Theme::flavors()` method
3. Accent validation uses `ThemeAccentMapper::validateAccent()`

**Testing**:
- Invalid combinations are corrected
- New valid combinations work
- Edge cases handled

## Migration Strategy

### Strategy 1: Backward Compatible Extension

**Scenario**: Adding new theme/flavor/accent options

**Migration**: **No migration needed**

**Rationale**:
- Existing data remains valid
- New options become available
- No data transformation required

**Procedure**:
1. Add new enum cases
2. Update CSS files
3. Update ThemeAccentMapper
4. Deploy
5. New options available immediately

### Strategy 2: Enum Value Removal

**Scenario**: Removing deprecated theme/flavor/accent

**Migration**: **Automatic correction**

**Rationale**:
- Invalid values detected on access
- Auto-corrected to defaults
- No manual migration needed

**Procedure**:
1. Remove enum case
2. Deploy
3. Invalid data auto-corrected on access
4. Monitor correction logs

### Strategy 3: Enum Value Rename

**Scenario**: Renaming enum value (e.g., `"mocha"` → `"mocha-dark"`)

**Migration**: **Data transformation required**

**Procedure**:
1. Create migration script:
   ```php
   DB::table('users')
       ->whereJsonContains('settings->flavor', 'mocha')
       ->update([
           'settings' => DB::raw("JSON_SET(settings, '$.flavor', 'mocha-dark')")
       ]);
   ```

2. Update enum value
3. Deploy migration
4. Deploy code
5. Verify data transformed

### Strategy 4: Schema Structure Change

**Scenario**: Adding new field to `UserSettingsData`

**Migration**: **Backward compatible**

**Procedure**:
1. Add field to `UserSettingsData` with default:
   ```php
   public function __construct(
       public Theme $theme = Theme::Catppuccin,
       public ThemeFlavor $flavor = ThemeFlavor::Mocha,
       public ThemeAccent $accent = ThemeAccent::Primary,
       public ?string $brightness = null, // New field
   ) {
       $this->brightness = $brightness ?? 'auto'; // Default
   }
   ```

2. Deploy
3. Existing data works (field is optional)
4. New data includes field

## Testing Checklist

### Before Extension

- [ ] Review existing theme structure
- [ ] Plan extension points
- [ ] Design CSS color scheme
- [ ] Verify contrast ratios (WCAG AA)

### During Extension

- [ ] Add enum cases
- [ ] Update CSS files
- [ ] Update ThemeAccentMapper
- [ ] Update tests
- [ ] Verify validation works

### After Extension

- [ ] Run full test suite
- [ ] Visual testing in browser
- [ ] Accessibility testing
- [ ] Performance testing
- [ ] Documentation update

## Example: Adding a New Theme

### Step 1: Add Theme Enum

```php
// app/Enums/Theme.php
case TokyoNight = 'tokyo-night';

public function label(): string
{
    return match ($this) {
        // ... existing ...
        self::TokyoNight => 'Tokyo Night',
    };
}

public function flavors(): array
{
    return match ($this) {
        // ... existing ...
        self::TokyoNight => [
            ThemeFlavor::Night,
            ThemeFlavor::Storm,
        ],
    };
}
```

### Step 2: Add ThemeFlavor (if new)

```php
// app/Enums/ThemeFlavor.php
case Night = 'night';
case Storm = 'storm';
```

### Step 3: Add CSS

```css
/* resources/css/themes/all-themes.css */
[data-theme="tokyo-night"][data-flavor="night"][data-accent="primary"] {
  --color-background: #1a1b26;
  --color-foreground: #c0caf5;
  /* ... other colors ... */
}
```

### Step 4: Update ThemeAccentMapper

```php
// app/Services/Theme/ThemeAccentMapper.php
public function getAvailableAccents(Theme $theme): array
{
    return match ($theme) {
        // ... existing ...
        Theme::TokyoNight => [
            ThemeAccent::Primary,
            ThemeAccent::Blue,
        ],
    };
}
```

### Step 5: Test

```bash
php artisan test --filter=Theme
```

## Best Practices

1. **Incremental Extensions**: Add one theme at a time
2. **Test Thoroughly**: Test all combinations
3. **Document Changes**: Update documentation
4. **Monitor Corrections**: Watch for validation corrections
5. **Backward Compatible**: Maintain backward compatibility

## Conclusion

✅ **Theme extension guide complete**

- Enum extension procedures
- CSS file update procedures
- ThemeAccentMapper update procedures
- Validation rule updates (automatic)
- Migration strategies for all scenarios
- Example extension walkthrough

## Recommendations

1. **Start Small**: Add one theme first
2. **Test Extensively**: Test all combinations
3. **Monitor Performance**: Ensure no performance degradation
4. **Update Documentation**: Keep extension guide current
