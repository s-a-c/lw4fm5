# View Composer Contract: Theme Injection

**Location**: `app/Providers/AppServiceProvider::boot()`
**Type**: Laravel View Composer

## Purpose

Inject theme data attributes (`data-theme`, `data-flavor`, `data-accent`) into all views for server-side initial load, preventing FOUC (Flash of Unstyled Content).

## Scope

- **Pattern**: `'*'` (all views)
- **Applies To**:
  - Main application pages
  - Filament admin panels
  - Authentication pages (Fortify)
  - Public pages
  - Theme preview page

## View Data Injected

The composer injects the following data into all views:

### `themeData: ThemeData`

- **Type**: `App\Data\ThemeData` (Spatie Data DTO)
- **Structure**:

```php
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

**Properties**:
- `theme`: `Theme` enum (`Theme::Catppuccin | Theme::Kanagawa`)
- `flavor`: `ThemeFlavor` enum (e.g., `ThemeFlavor::Mocha`, `ThemeFlavor::Latte`, `ThemeFlavor::Wave`)
- `accent`: `ThemeAccent` enum (`ThemeAccent::Primary | ThemeAccent::Blue | ThemeAccent::Red | ThemeAccent::Green`)
- `isLight()`: Method returning `bool` - `true` if flavor is light (Latte, Lotus)

## Implementation Logic

### 1. User Authentication Check

- If authenticated: Read `$user->settings`
- If not authenticated: Use defaults (`Theme::Catppuccin`, `ThemeFlavor::Mocha`, `ThemeAccent::Primary`)

### 2. Validation

- **Validation Timing**: Validated on every access (whenever settings are read: View Composer, Livewire mount, direct model access, etc.)
- Validate theme/flavor/accent enum values
- Validate theme/flavor combination (flavor must belong to theme)
- On invalid: Reset to defaults silently (`Theme::Catppuccin`, `ThemeFlavor::Mocha`, `ThemeAccent::Primary`)
- Persist corrected settings to database silently
- Performance impact is minimal since validation is lightweight

### 3. Data Preparation

- Extract theme, flavor, accent enum values
- Create `ThemeData` DTO instance with validated enums

### 4. View Injection

- Inject `ThemeData` DTO into view as `themeData`
- Layout templates read `themeData` and apply to `<html>` element

## Layout Template Usage

Layout templates should apply theme data as follows:

```blade
<html
    lang="en"
    data-theme="{{ $themeData->theme->value ?? 'catppuccin' }}"
    data-flavor="{{ $themeData->flavor->value ?? 'mocha' }}"
    data-accent="{{ $themeData->accent->value ?? 'primary' }}"
    @class(['dark' => !($themeData->isLight() ?? false)])
>

```

## Performance Considerations

- **Database Query**: Single query per request (if user is authenticated)
- **Caching**: User model may be cached by Laravel's authentication system
- **Unauthenticated Requests**: No database query, uses defaults immediately

## Error Handling

- **Invalid Settings**: Silently reset to defaults, no error shown to user
- **Database Errors**: Falls back to defaults, logs error
- **Missing User**: Uses defaults (for unauthenticated users)

## Integration Points

### Filament Panels

Filament panels receive theme data via View Composer. Filament's layout templates should read `themeData` and apply attributes to `<html>` element.

### Authentication Pages

Fortify authentication pages use Livewire components. View Composer ensures theme data is available to these components.

### Theme Preview Page

Theme preview page (`/themes/preview`) may override server-injected theme with session storage values (client-side JavaScript takes precedence for preview page only).

## Testing

- **Unit Tests**: Test View Composer logic with various user settings
- **Feature Tests**: Verify theme data is injected into views
- **Browser Tests**: Verify no FOUC on page load
