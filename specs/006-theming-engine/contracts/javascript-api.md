# JavaScript API Contract: Theme Management

**File**: `resources/js/app.js`
**Purpose**: Client-side theme initialization and fallback

## Functions

### `initializeTheme(): void`

- **Purpose**: Initialize theme data attributes on page load
- **Trigger**: `DOMContentLoaded` event
- **Behavior**:
  1. Read `data-theme`, `data-flavor`, `data-accent` from `<html>` element
  2. If missing, set defaults:
     - `data-theme`: `'catppuccin'`
     - `data-flavor`: `'mocha'`
     - `data-accent`: `'primary'`
  3. Manage `dark` class based on flavor:
     - Light flavors: `latte`, `lotus` → Remove `dark` class
     - Dark flavors: All others → Add `dark` class

## DOM API

### HTML Element Data Attributes

The `<html>` element should have the following data attributes:

- `data-theme`: Theme identifier
- `data-flavor`: Flavor identifier
- `data-accent`: Accent identifier

### Dark Mode Class

The `<html>` element's `classList` should include `dark` for dark themes.

## Session Storage API (Theme Preview Page Only)

### `sessionStorage.setItem(key, value)`

- **Keys**:
  - `'theme'`: Theme value
  - `'flavor'`: Flavor value
  - `'accent'`: Accent value
- **Scope**: Theme preview page only
- **Lifetime**: Browser session (cleared on tab close)

### `sessionStorage.getItem(key)`

- **Purpose**: Read theme preferences on theme preview page load
- **Fallback**: Use defaults if not set

## CSS Integration

CSS uses attribute selectors to apply theme colors:

```css
[data-theme='catppuccin'][data-flavor='mocha'] {
  --color-zinc-900: #1e1e2e;
  /* ... */
}
```

JavaScript updates data attributes, CSS automatically applies new colors.

## Livewire Integration

Livewire components update DOM attributes via `$this->js()`:

```php
$this->js(<<<'JS'
    const r = document.documentElement;
    r.dataset.theme = $wire.theme;
    r.dataset.flavor = $wire.flavor;
    r.dataset.accent = $wire.accent;
JS);

```

## Performance

- **Initialization**: Runs once on page load, minimal overhead
- **Live Updates**: Direct DOM manipulation, <200ms latency
- **CSS Application**: Native browser performance, no JavaScript overhead

## Browser Compatibility

- **Data Attributes**: Supported in all modern browsers
- **Session Storage**: Supported in all modern browsers (IE11+)
- **classList API**: Supported in all modern browsers

## Error Handling

- **Missing Attributes**: Fallback to defaults
- **Invalid Values**: CSS won't match, falls back to default theme
- **Session Storage Unavailable**: Falls back to server-injected values
