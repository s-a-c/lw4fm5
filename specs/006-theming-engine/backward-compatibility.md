# Backward Compatibility Strategy

**Task**: T028c [FR-053]
**Status**: Complete

## Overview

This document describes the backward compatibility strategy for future `users.settings` schema changes, including migration paths, data transformation, and rollback procedures.

## Current Schema

**Column**: `users.settings` (JSON, nullable)

**Structure**:
```json
{
  "theme": "catppuccin",
  "flavor": "mocha",
  "accent": "primary"
}
```

**Validation**:
- All values must be valid enum cases
- Theme/flavor combinations must be valid
- Accent must be valid for theme/flavor

## Migration Strategy

### Scenario 1: Enum Value Removal

**Example**: `ThemeFlavor::Dragon` is removed

**Migration Path**:
1. **Validation**: On access, invalid enum values are detected
2. **Correction**: Invalid values are reset to defaults
3. **Persistence**: Corrected values are saved automatically

**Code**:
```php
// In ThemeService::resolveThemeData()
if (!in_array($flavor, $theme->flavors())) {
    $flavor = $theme->flavors()[0]; // First available flavor
    $wasCorrected = true;
}
```

**Rollback**:
- Re-add enum value
- Existing data becomes valid again
- No manual migration needed

### Scenario 2: Enum Value Addition

**Example**: New theme `Theme::TokyoNight` is added

**Migration Path**:
1. **No Migration Required**: Existing data remains valid
2. **New Options Available**: New theme becomes selectable
3. **Backward Compatible**: Old data continues to work

**Rollback**:
- Remove enum value
- Existing data with new value becomes invalid
- Auto-correction handles invalid data

### Scenario 3: Enum Value Rename

**Example**: `"mocha"` → `"mocha-dark"`

**Migration Path**:
1. **Data Transformation**: Create migration to update values
2. **Validation**: Invalid values are auto-corrected
3. **Persistence**: Corrected values are saved

**Migration Script**:
```php
// database/migrations/XXXX_XX_XX_rename_mocha_to_mocha_dark.php
DB::table('users')
    ->whereJsonContains('settings->flavor', 'mocha')
    ->update([
        'settings' => DB::raw("JSON_SET(settings, '$.flavor', 'mocha-dark')")
    ]);
```

**Rollback**:
- Reverse migration script
- Or: Re-add old enum value as alias

### Scenario 4: Schema Structure Change

**Example**: Add new field `brightness: 'auto' | 'light' | 'dark'`

**Migration Path**:
1. **Add Field**: Update `UserSettingsData` DTO
2. **Default Value**: New field defaults to `'auto'`
3. **Backward Compatible**: Existing data works (field is optional)

**Code**:
```php
// In UserSettingsData
public function __construct(
    public Theme $theme = Theme::Catppuccin,
    public ThemeFlavor $flavor = ThemeFlavor::Mocha,
    public ThemeAccent $accent = ThemeAccent::Primary,
    public ?string $brightness = null, // New optional field
) {
    $this->brightness = $brightness ?? 'auto'; // Default
}
```

**Rollback**:
- Remove field from DTO
- Existing data continues to work (field ignored)

## Data Transformation

### Automatic Transformation

**Location**: `ThemeService::resolveThemeData()`

**Process**:
1. Read user settings from database
2. Validate all enum values
3. Correct invalid combinations
4. Persist corrected values (if changed)

**Benefits**:
- No manual migration scripts needed
- Transformation happens on access
- Lazy migration (only when user accesses)

### Manual Transformation

**When Needed**:
- Bulk updates
- Performance optimization
- Data cleanup

**Example**:
```php
// Artisan command: theme:migrate-data
User::chunk(100, function ($users) {
    foreach ($users as $user) {
        $settings = $user->settings;
        $corrected = ThemeService::resolveThemeData($settings);

        if ($corrected->theme !== $settings->theme) {
            $user->updateQuietly(['settings' => $corrected]);
        }
    }
});
```

## Rollback Procedures

### Rollback Strategy 1: Code Rollback

**Scenario**: Deploy breaks existing data

**Procedure**:
1. Revert code to previous version
2. Invalid data is auto-corrected on access
3. No database rollback needed

**Risk**: Low (validation handles invalid data)

### Rollback Strategy 2: Database Rollback

**Scenario**: Migration script corrupts data

**Procedure**:
1. Restore database from backup
2. Revert code to previous version
3. Re-run validation/correction

**Risk**: Medium (requires backup)

### Rollback Strategy 3: Data Correction

**Scenario**: Invalid data in production

**Procedure**:
1. Run correction script:
   ```php
   User::chunk(100, function ($users) {
       foreach ($users as $user) {
           $corrected = ThemeService::resolveThemeData($user->settings);
           $user->updateQuietly(['settings' => $corrected]);
       }
   });
   ```
2. Monitor correction logs
3. Verify data integrity

**Risk**: Low (correction is safe)

## Testing

### Backward Compatibility Tests

**File**: `tests/Feature/Theme/ThemeValidationTest.php`

**Tests**:
- Invalid enum values are corrected
- Invalid combinations are corrected
- Null/empty states are handled
- Corrupted data is corrected

### Migration Tests

**Recommendation**: Create migration test suite:

```php
test('migration handles removed enum value', function () {
    // Create user with old enum value
    $user = User::factory()->create([
        'settings' => ['theme' => 'catppuccin', 'flavor' => 'old-flavor']
    ]);

    // Access should correct invalid value
    $settings = $user->settings;
    expect($settings->flavor)->toBe(ThemeFlavor::Mocha); // Default
});
```

## Best Practices

1. **Always Provide Defaults**: New fields should have defaults
2. **Validate on Access**: Don't assume data is valid
3. **Log Corrections**: Track what was corrected and why
4. **Test Migrations**: Test with real data before deploying
5. **Monitor Corrections**: Alert on high correction rates

## Conclusion

✅ **Backward compatibility is maintained**

- Automatic validation and correction
- Lazy migration (on access)
- Safe rollback procedures
- Comprehensive testing
- Clear migration paths
