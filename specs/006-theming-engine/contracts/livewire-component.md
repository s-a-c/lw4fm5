# Livewire Component Contract: Appearance Settings

**Component**: `resources/views/livewire/settings/appearance.blade.php`
**Type**: Livewire Volt Functional Component

## Public Properties

### `theme: string`

- **Type**: `string` (enum value)
- **Default**: User's saved theme or `'catppuccin'`
- **Enum**: `Theme::Catppuccin | Theme::Kanagawa`
- **Binding**: `wire:model.live="theme"`
- **Behavior**: Updates available flavors when changed

### `flavor: string`

- **Type**: `string` (enum value)
- **Default**: User's saved flavor or `'mocha'`
- **Enum**: `ThemeFlavor::*` (dependent on selected theme)
- **Binding**: `wire:model.live="flavor"`
- **Behavior**: Validated on every access (whenever settings are read), auto-corrected if invalid for selected theme

### `accent: string`

- **Type**: `string` (enum value)
- **Default**: User's saved accent or `'primary'`
- **Enum**: `ThemeAccent::Primary | ThemeAccent::Blue | ThemeAccent::Red | ThemeAccent::Green`
- **Binding**: `wire:model.live="accent"`

### `availableFlavors: array<ThemeFlavor>`

- **Type**: `array` (Livewire requires array, not Collection)
- **PHPDoc**: `/** @var array<ThemeFlavor> */`
- **Default**: Flavors for selected theme (via `Theme::flavors()`)
- **Behavior**: Updated when theme changes
- **Note**: Array contains `ThemeFlavor` enum instances, not strings

## Public Methods

### `mount(): void`

- **Purpose**: Initialize component with user's saved settings
- **Behavior**:
  - Reads authenticated user's settings
  - Falls back to `UserSettingsData` defaults if null
  - Populates `theme`, `flavor`, `accent` properties
  - Calls `updateAvailableFlavors()`

### `updateAvailableFlavors(): void`

- **Purpose**: Update available flavors based on selected theme
- **Behavior**:
  - Gets flavors for current theme via `Theme::from($this->theme)->flavors()`
  - If current flavor is invalid for new theme, resets to first available flavor
  - Triggers save if flavor was auto-corrected

### `updated(string $property, mixed $value): void`

- **Purpose**: Handle property updates with auto-save and live preview
- **Parameters**:
  - `$property`: Property name that changed (`'theme'`, `'flavor'`, or `'accent'`)
  - `$value`: New value
- **Behavior**:
  1. If `$property === 'theme'`: Calls `updateAvailableFlavors()`
  2. Loads user settings (or creates new `UserSettingsData`)
  3. Updates DTO with new value
  4. Saves to database via `$user->save()`
  5. Updates DOM via `$this->js()` for instant preview:
     - Sets `document.documentElement.dataset.theme`
     - Sets `document.documentElement.dataset.flavor`
     - Sets `document.documentElement.dataset.accent`
     - Manages `dark` class based on flavor
  6. Dispatches Flux toast notification

## JavaScript API (Client-Side)

### DOM Data Attributes

The component sets the following data attributes on `<html>` element:

- `data-theme`: Theme value (`'catppuccin'` | `'kanagawa'`)
- `data-flavor`: Flavor value (e.g., `'mocha'`, `'latte'`, `'wave'`)
- `data-accent`: Accent value (`'primary'` | `'blue'` | `'red'` | `'green'`)

### Dark Mode Class

The component manages the `dark` class on `<html>` element:

- **Light flavors**: `latte`, `lotus` → Removes `dark` class
- **Dark flavors**: All others → Adds `dark` class

## Events

### Dispatched Events

- `flux-toast`: Dispatched after theme update
  - **Text**: `'Theme updated!'`
  - **Variant**: `'success'`

## Validation

### Automatic Validation

- **Validation Timing**: Validated on every access (whenever settings are read: View Composer, Livewire mount, direct model access, etc.)
- **Theme/Flavor Combination**: Validated in `updateAvailableFlavors()` and on every access
  - If flavor doesn't belong to theme, resets to first available flavor
- **Enum Values**: Validated via `Theme::from()`, `ThemeFlavor::from()`, `ThemeAccent::from()`
  - Throws exception if invalid (handled by Livewire)
- **Before Persistence**: Validated BEFORE database persistence (FR-017) - invalid data rejected at input boundary

## Error Handling

- **Invalid Enum Value**: Livewire catches exception, shows error message
- **Database Save Failure**: Livewire catches exception, shows error message
- **Unauthenticated User**: Component requires authentication (handled by route middleware)

## Performance

- **Update Latency**: <200ms (SC-002)
- **Database Writes**: Immediate on property change (no debouncing)
- **DOM Updates**: Synchronous via `$this->js()` (no network round-trip)
