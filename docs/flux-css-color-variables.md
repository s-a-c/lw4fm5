# Flux UI CSS Color Variables Documentation

This document provides a comprehensive reference for all CSS color variables provided and used by Livewire Flux UI components.

## Table of Contents

- [Core Accent Color Variables](#core-accent-color-variables)
- [Temporary/Local Color Variables](#temporarylocal-color-variables)
- [Base Color Variables](#base-color-variables)
- [Supported Color Schemes](#supported-color-schemes)
- [Usage Examples](#usage-examples)
- [Customization Guide](#customization-guide)

---

## Core Accent Color Variables

Flux uses three primary CSS variables for accent colors, which control the appearance of primary buttons, links, tabs, and other interactive elements.

### `--color-accent`

**Description:** The main accent color used as the background for primary buttons and other primary interactive elements.

**Default Values:**
- Light mode: `var(--color-zinc-800)`
- Dark mode: `var(--color-white)`

**Usage:** Primary button backgrounds, switch checked states, checkbox/radio checked states.

**Example:**
```css
@theme {
    --color-accent: var(--color-zinc-800);
}

@layer theme {
    .dark {
        --color-accent: var(--color-white);
    }
}
```

### `--color-accent-content`

**Description:** A (typically) darker hue used for text content because it's more readable. Used for text on light backgrounds, links, headings, and navigation items.

**Default Values:**
- Light mode: `var(--color-zinc-800)`
- Dark mode: `var(--color-white)`

**Usage:** 
- Link text color (when `accent="true"`)
- Heading text color (when accent is enabled)
- Navigation item text when active/current
- Checkbox/radio indicator backgrounds

**Example:**
```css
@theme {
    --color-accent-content: var(--color-zinc-800);
}

@layer theme {
    .dark {
        --color-accent-content: var(--color-white);
    }
}
```

### `--color-accent-foreground`

**Description:** The color of (typically) text content on top of an accent-colored background. Used for text that appears on `--color-accent` backgrounds.

**Default Values:**
- Light mode: `var(--color-white)`
- Dark mode: `var(--color-zinc-800)`

**Usage:**
- Primary button text color
- Text on accent-colored backgrounds
- Checkbox/radio checkmark/minus icons
- Switch thumb color when checked

**Example:**
```css
@theme {
    --color-accent-foreground: var(--color-white);
}

@layer theme {
    .dark {
        --color-accent-foreground: var(--color-zinc-800);
    }
}
```

---

## Temporary/Local Color Variables

These variables are created dynamically within component scopes and are not part of the global theme.

### `--hover-fill`

**Description:** A semi-transparent fill color used for hover states on navigation items. Created using `color-mix()` with the accent content color.

**Usage:** Navbar item hover backgrounds when the item is current/active.

**Definition:**
```css
--hover-fill: color-mix(in_oklab, var(--color-accent-content), transparent 90%);
```

**Location:** `vendor/livewire/flux/stubs/resources/views/flux/navbar/item.blade.php`

**Example Usage:**
```blade
<div class="[--hover-fill:color-mix(in_oklab,_var(--color-accent-content),_transparent_90%)]">
    <!-- Navbar item with hover fill -->
</div>
```

---

## Base Color Variables

Flux uses **zinc** as the default base color throughout the application. All standard Tailwind color variables are available, but Flux components are hard-coded to use zinc shades.

### Default Base Color (Zinc)

Flux components reference these zinc color variables:

- `--color-zinc-50` through `--color-zinc-950` (standard Tailwind scale)

**Note:** To change the base color from zinc to another gray scale (e.g., slate, gray, neutral, stone), you must redefine all zinc variables in your `@theme` block:

```css
@theme {
  --color-zinc-50: var(--color-slate-50);
  --color-zinc-100: var(--color-slate-100);
  --color-zinc-200: var(--color-slate-200);
  --color-zinc-300: var(--color-slate-300);
  --color-zinc-400: var(--color-slate-400);
  --color-zinc-500: var(--color-slate-500);
  --color-zinc-600: var(--color-slate-600);
  --color-zinc-700: var(--color-slate-700);
  --color-zinc-800: var(--color-slate-800);
  --color-zinc-900: var(--color-slate-900);
  --color-zinc-950: var(--color-slate-950);
}
```

---

## Supported Color Schemes

Flux supports the following color schemes for accent colors via the `color` prop on components like `<flux:button>` and `<flux:accent>`:

### Neutral Grays
- `slate`
- `gray`
- `zinc` (default)
- `neutral`
- `stone`

### Warm Colors
- `red`
- `orange`
- `amber`
- `yellow`
- `lime`

### Cool Colors
- `green`
- `emerald`
- `teal`
- `cyan`
- `sky`
- `blue`
- `indigo`
- `violet`
- `purple`

### Vibrant Colors
- `fuchsia`
- `pink`
- `rose`

### Color Scheme Mappings

Each color scheme defines all three accent variables with appropriate light/dark mode values. Here's an example for the `blue` color:

**Light Mode:**
- `--color-accent`: `var(--color-blue-500)`
- `--color-accent-content`: `var(--color-blue-600)`
- `--color-accent-foreground`: `var(--color-white)`

**Dark Mode:**
- `--color-accent`: `var(--color-blue-500)`
- `--color-accent-content`: `var(--color-blue-400)`
- `--color-accent-foreground`: `var(--color-white)`

**Complete Color Definitions:**

See `vendor/livewire/flux/stubs/resources/views/flux/accent.blade.php` and `vendor/livewire/flux/stubs/resources/views/flux/button/index.blade.php` for the complete mapping of all 22 supported color schemes.

---

## Usage Examples

### Using Accent Variables in Custom CSS

```css
/* Custom button style using accent variables */
.custom-button {
    background-color: var(--color-accent);
    color: var(--color-accent-foreground);
}

.custom-link {
    color: var(--color-accent-content);
    text-decoration-color: color-mix(in_oklab, var(--color-accent-content), transparent 80%);
}
```

### Using Accent Variables in Tailwind Classes

```blade
<!-- Direct CSS variable reference -->
<button class="bg-[var(--color-accent)] text-[var(--color-accent-foreground)]">
    Click me
</button>

<!-- Using Tailwind utility classes (if configured) -->
<button class="bg-accent text-accent-foreground">
    Click me
</button>
```

### Setting Accent Color via Component Props

```blade
<!-- Button with color prop -->
<flux:button variant="primary" color="blue">Blue Button</flux:button>

<!-- Accent wrapper component -->
<flux:accent color="red">
    <!-- All child components will use red accent -->
</flux:accent>
```

### Customizing Accent Colors Globally

```css
/* resources/css/app.css */
@theme {
    --color-accent: var(--color-blue-600);
    --color-accent-content: var(--color-blue-600);
    --color-accent-foreground: var(--color-white);
}

@layer theme {
    .dark {
        --color-accent: var(--color-blue-500);
        --color-accent-content: var(--color-blue-400);
        --color-accent-foreground: var(--color-white);
    }
}
```

---

## Customization Guide

### Step 1: Choose Your Accent Color

Decide on your primary accent color. You can use:
- A predefined Tailwind color (recommended)
- A custom color value

### Step 2: Define Variables in `@theme` Block

Add your accent color variables to the `@theme` block in `resources/css/app.css`:

```css
@theme {
    --color-accent: var(--color-your-color-600);
    --color-accent-content: var(--color-your-color-600);
    --color-accent-foreground: var(--color-white);
}
```

### Step 3: Define Dark Mode Variables

Add dark mode overrides in the `@layer theme` block:

```css
@layer theme {
    .dark {
        --color-accent: var(--color-your-color-500);
        --color-accent-content: var(--color-your-color-400);
        --color-accent-foreground: var(--color-white);
    }
}
```

### Step 4: Test Your Changes

Verify that:
- Primary buttons use the new accent color
- Links and active navigation items use the accent content color
- Text on accent backgrounds is readable (accent foreground color)

### Recommended Color Combinations

**For Light Backgrounds:**
- Accent: Use 600-800 shades for good contrast
- Content: Use 600-800 shades (same or slightly darker)
- Foreground: Use white or very light colors

**For Dark Backgrounds:**
- Accent: Use 400-500 shades for visibility
- Content: Use 300-400 shades for readability
- Foreground: Use white or very light colors

---

## Component-Specific Usage

### Buttons

Primary buttons use:
- Background: `--color-accent`
- Text: `--color-accent-foreground`
- Hover: `color-mix(in_oklab, var(--color-accent), transparent 10%)`

### Links

Links with `accent="true"` use:
- Text color: `--color-accent-content`
- Underline: `color-mix(in_oklab, var(--color-accent-content), transparent 80%)`

### Navigation Items

Active/current navigation items use:
- Text: `--color-accent-content`
- Background (hover): `--hover-fill` (derived from accent-content)
- Indicator: `--color-accent-content`

### Form Controls

**Checkboxes & Radio Buttons:**
- Checked background: `--color-accent`
- Checkmark/minus icon: `--color-accent-foreground`

**Switches:**
- Checked track: `--color-accent`
- Checked thumb: `--color-accent-foreground`

### Headings

Headings with accent enabled use:
- Text color: `--color-accent-content`

---

## File Locations

- **Core CSS Variables:** `vendor/livewire/flux/dist/flux.css`
- **Accent Component:** `vendor/livewire/flux/stubs/resources/views/flux/accent.blade.php`
- **Button Component:** `vendor/livewire/flux/stubs/resources/views/flux/button/index.blade.php`
- **Checkbox Indicator:** `vendor/livewire/flux/stubs/resources/views/flux/checkbox/indicator.blade.php`
- **Radio Indicator:** `vendor/livewire/flux/stubs/resources/views/flux/radio/indicator.blade.php`
- **Switch Component:** `vendor/livewire/flux/stubs/resources/views/flux/switch.blade.php`
- **Navbar Item:** `vendor/livewire/flux/stubs/resources/views/flux/navbar/item.blade.php`
- **Navlist Item:** `vendor/livewire/flux/stubs/resources/views/flux/navlist/item.blade.php`
- **Sidebar Item:** `vendor/livewire/flux/stubs/resources/views/flux/sidebar/item.blade.php`
- **Link Component:** `vendor/livewire/flux/stubs/resources/views/flux/link.blade.php`
- **Heading Component:** `vendor/livewire/flux/stubs/resources/views/flux/heading.blade.php`

---

## References

- [Flux Theming Documentation](https://flux.laravel.com/docs/theming)
- [Flux Accent Color Documentation](https://flux.laravel.com/docs/theming#accent-color)
- [Tailwind CSS Color Variables](https://tailwindcss.com/docs/customizing-colors)

---

**Last Updated:** Based on Livewire Flux v2.x

