# Most Popular Software Colour Schemes

-----

<details>
<summary>Expand for Table of Contents</summary>

- [Most Popular Software Colour Schemes](#most-popular-software-colour-schemes)
  - [1. Global vs UK](#1-global-vs-uk)
    - [1.1. **Part 1: Top 10 Worldwide (Developer \& App Themes)**](#11-part-1-top-10-worldwide-developer--app-themes)
    - [1.2. **Part 2: Top 5 UK Authentic (Web \& Brand Schemes)**](#12-part-2-top-5-uk-authentic-web--brand-schemes)
  - [2. Project Kit](#2-project-kit)
    - [2.1. Summary of the Logic Flow](#21-summary-of-the-logic-flow)
    - [2.2. **The Tailwind Setup (The Bridge)**](#22-the-tailwind-setup-the-bridge)
    - [2.3. **The CSS Definitions**](#23-the-css-definitions)
    - [2.4. **The JS Data Source**](#24-the-js-data-source)
    - [2.5. **The Livewire Flux Switcher**](#25-the-livewire-flux-switcher)

</details>

-----

## 1. Global vs UK

There is a distinct split in how "colour schemes" are defined in 2024/2025.

  * **Worldwide (Developer/App Context):** Refers to **IDE & System Themes** (Dark mode, syntax highlighting, terminal "ricing"). There is **no significant difference** between UK and Global developers; the community is universal.
  * **UK (Web/Design Context):** Refers to **Digital Heritage & Design Systems**. The UK has a world-famous tradition of "functional, high-contrast" digital design (pioneered by GDS and the BBC) that is distinct from the neon/pastel global trends.

Here is the **Top 10 Global Developer Schemes** (which dominate the UK tech scene) versus the **Top 5 Authentic UK Design Systems** that define the British web aesthetic.

-----

### 1.1. **Part 1: Top 10 Worldwide (Developer & App Themes)**

*These are the most popular cross-platform schemes for VS Code, Neovim, and Terminal aesthetics globally.*

| Rank | Theme | Vibe / Aesthetic | Best For |
| :--- | :--- | :--- | :--- |
| **1** | **Catppuccin** | **Warm Pastel.** Low-contrast, "soothing" mid-tones. Comes in 4 flavors (Latte, Frappé, Macchiato, Mocha). | Long coding sessions; ease on eyes. |
| **2** | **Tokyo Night** | **Neon City.** Deep blue-purple background with sharp neon accents. Inspired by cyberpunk aesthetics. | VS Code & Neovim users who want "pop". |
| **3** | **Dracula** | **Vampire Dark.** A universal classic. Dark purple background with pink/green/yellow accents. | Maximum compatibility (avail. on 200+ apps). |
| **4** | **Kanagawa** | **Japanese Art.** Desaturated, "muddy" teals and warm beiges. Inspired by the *Great Wave* painting. | Minimalists; removing digital glare. |
| **5** | **Gruvbox** | **Retro Computing.** Reddish-brown, earthy, "warm hardware" feel. | VIM users; retro-tech enthusiasts. |
| **6** | **Nord** | **Arctic Blue.** Cool, icy, flat bluish-greys. Very low eye strain but low contrast. | Linux systems; clean UI design. |
| **7** | **Rosé Pine** | **Minimalist Soho.** Soft, muted rosy warm tones. "Distraction-free" aesthetic. | Writers and focus-heavy developers. |
| **8** | **One Dark Pro** | **Atom Legacy.** The classic "default" for many years. Neutral greys and safe distinct colours. | Enterprise environments; standard tooling. |
| **9** | **Monokai Pro** | **High Contrast.** The OG Sublime Text evolution. Dark grey with intense, sharp primary colours. | Python/JS devs who need clear syntax diffs. |
| **10** | **Solarized** | **Scientific.** Precision-engineered for contrast. (Dark & Light). Iconic but fading in popularity. | Old-school sysadmins; outdoor usage (Light). |

-----

### 1.2. **Part 2: Top 5 UK Authentic (Web & Brand Schemes)**

*While UK developers use the themes above, the "UK Web Aesthetic" is defined by these massive Design Systems. These are the gold standard for "British Digital Identity."*

| Rank | Scheme | **Identity & Usage** |
| :--- | :--- | :--- |
| **1** | **GOV.UK** | **The Gold Standard.** The [GDS Design System](https://design-system.service.gov.uk/) is arguably the most famous functional web scheme in the world. Accessible, high-contrast, and strictly functional. |
| **2** | **Transport for London** | **The Tube.** Iconic Johnston Blue and Roundel Red. Used in mapping apps and transit data visualizations worldwide. |
| **3** | **NHS Digital** | **National Health.** A strict Blue and Green palette that signals "trust" and "medical authority" in the UK. |
| **4** | **Financial Times** | **FT Pink.** The only major global brand to use a "salmon pink" background as its primary digital canvas ("Paper"). |
| **5** | **The Guardian** | **Egyptian Navy.** A distinct mix of Deep Navy and Bright Yellow, creating a high-contrast "tabloid vs broadsheet" digital hybrid. |

-----

## 2. Project Kit

Here is the consolidated **"Project Kit."** You can copy this entire block and split it into the respective files in your Laravel project.

### 2.1. Summary of the Logic Flow

1. User Selects "Kanagawa Lotus" (Light Mode).
1. JS: Finds kanagawa-lotus in registry -> sees mode: 'light'.
1. JS: Sets data-theme="kanagawa-lotus".
1. JS: Removes .dark class from <html>.
1. Filament/Flux: Renders <div class="bg-gray-50"> (Because it's light mode).
1. CSS: [data-theme="kanagawa-lotus"] maps --bg-50 to #f2ecbc (Lotus White).
1. Result: Correct Light Cream background.
1. User Selects "Kanagawa Wave" (Dark Mode).
1. JS: Finds kanagawa-wave in registry -> sees mode: 'dark'.
1. JS: Sets data-theme="kanagawa-wave".
1. JS: Adds .dark class to <html>.
1. Filament/Flux: Renders <div class="dark:bg-gray-900"> (Because it's dark mode).
1. CSS: [data-theme="kanagawa-wave"] maps --bg-900 to #0d0c0c (Deep Dark).
1. Result: Correct Dark Blue/Black background.

### 2.2. **The Tailwind Setup (The Bridge)**

**File:** `resources/css/app.css`
*This maps Filament (Gray) and Flux (Zinc) to your dynamic variables.*

```css
@import "tailwindcss";

/* --- IMPORT YOUR THEME DEFINITIONS HERE --- */
@import "./themes/all-themes.css";

@theme {
  /* BRIDGE: Redirect Framework Colors
     Flux uses 'zinc'. Filament uses 'gray'.
     We map BOTH to our dynamic --bg scale.
  */

  /* The Zinc Bridge (Flux) */
  --color-zinc-50: var(--bg-50);
  --color-zinc-100: var(--bg-100);
  --color-zinc-200: var(--bg-200);
  --color-zinc-300: var(--bg-300);
  --color-zinc-400: var(--bg-400);
  --color-zinc-500: var(--bg-500);
  --color-zinc-600: var(--bg-600);
  --color-zinc-700: var(--bg-700);
  --color-zinc-800: var(--bg-800);
  --color-zinc-900: var(--bg-900);
  --color-zinc-950: var(--bg-950);

  /* The Gray Bridge (Filament) */
  --color-gray-50: var(--bg-50);
  --color-gray-100: var(--bg-100);
  --color-gray-200: var(--bg-200);
  --color-gray-300: var(--bg-300);
  --color-gray-400: var(--bg-400);
  --color-gray-500: var(--bg-500);
  --color-gray-600: var(--bg-600);
  --color-gray-700: var(--bg-700);
  --color-gray-800: var(--bg-800);
  --color-gray-900: var(--bg-900);
  --color-gray-950: var(--bg-950);

  /* Primary Brand Bridge */
  --color-indigo-500: var(--primary-500);
  --color-indigo-600: var(--primary-600);
}

```

-----

### 2.3. **The CSS Definitions**

**File:** `resources/css/themes/all-themes.css`
*Contains all 15+ variations. Note: 50 is always light, 950 is always dark.*

```css
@layer base {
    /* =========================================
       FAMILY: CATPPUCCIN (Warm Pastel)
       ========================================= */
    /* LATTE (Light) */
    [data-theme="catppuccin-latte"] {
        --bg-50: #eff1f5; --bg-100: #e6e9ef; --bg-200: #dce0e8;
        --bg-300: #bcc0cc; --bg-400: #acb0be; --bg-500: #9ca0b0;
        --bg-600: #8c8fa1; --bg-700: #7287fd; --bg-800: #5c5f77;
        --bg-900: #4c4f69; --bg-950: #000000;
        --primary-500: #8839ef; --primary-600: #ea76cb;
    }
    /* MOCHA (Dark) */
    [data-theme="catppuccin-mocha"] {
        --bg-50: #cdd6f4; --bg-100: #bac2de; --bg-200: #a6adc8;
        --bg-300: #9399b2; --bg-400: #585b70; --bg-500: #45475a;
        --bg-600: #313244; --bg-700: #1e1e2e; --bg-800: #181825;
        --bg-900: #11111b; --bg-950: #0d0d15;
        --primary-500: #cba6f7; --primary-600: #f5c2e7;
    }
    /* FRAPPE (Dark) */
    [data-theme="catppuccin-frappe"] {
        --bg-50: #c6d0f5; --bg-100: #b5bfe2; --bg-200: #a5adce;
        --bg-300: #949cbb; --bg-400: #626880; --bg-500: #51576d;
        --bg-600: #414559; --bg-700: #303446; --bg-800: #292c3c;
        --bg-900: #232634; --bg-950: #181920;
        --primary-500: #ca9ee6; --primary-600: #f4b8e4;
    }
    /* MACCHIATO (Dark) */
    [data-theme="catppuccin-macchiato"] {
        --bg-50: #cad3f5; --bg-100: #b8c0e0; --bg-200: #a5adcb;
        --bg-300: #939ab7; --bg-400: #5b6078; --bg-500: #494d64;
        --bg-600: #363a4f; --bg-700: #24273a; --bg-800: #1e2030;
        --bg-900: #181926; --bg-950: #0f1019;
        --primary-500: #c6a0f6; --primary-600: #f5bde6;
    }

    /* =========================================
       FAMILY: ASIAN (Tokyo / Kanagawa)
       ========================================= */
    /* TOKYO NIGHT (Dark) */
    [data-theme="tokyo-night"] {
        --bg-50: #c0caf5; --bg-100: #a9b1d6; --bg-200: #787c99;
        --bg-300: #565f89; --bg-400: #414868; --bg-500: #24283b;
        --bg-600: #1a1b26; --bg-700: #16161e; --bg-800: #0f0f14;
        --bg-900: #0a0a0c; --bg-950: #000000;
        --primary-500: #7aa2f7; --primary-600: #bb9af7;
    }
    /* TOKYO DAY (Light) */
    [data-theme="tokyo-day"] {
        --bg-50: #e1e2e7; --bg-100: #d0d5e3; --bg-200: #b4b5b9;
        --bg-300: #9699a3; --bg-400: #58585a; --bg-500: #3760bf;
        --bg-600: #2e3c64; --bg-700: #1a1b26; --bg-800: #0f0f14;
        --bg-900: #000000; --bg-950: #000000;
        --primary-500: #3760bf; --primary-600: #b4b5b9;
    }
    /* KANAGAWA WAVE (Dark) */
    [data-theme="kanagawa-wave"] {
        --bg-50: #dcd7ba; --bg-100: #c8c093; --bg-200: #727169;
        --bg-300: #54546d; --bg-400: #363646; --bg-500: #2d4f67;
        --bg-600: #223249; --bg-700: #1f1f28; --bg-800: #16161d;
        --bg-900: #0d0c0c; --bg-950: #000000;
        --primary-500: #7e9cd8; --primary-600: #6a9589;
    }
    /* KANAGAWA LOTUS (Light) */
    [data-theme="kanagawa-lotus"] {
        --bg-50: #f2ecbc; --bg-100: #e5ddb0; --bg-200: #d5cea3;
        --bg-300: #8a9977; --bg-400: #6f894e; --bg-500: #625e4e;
        --bg-600: #5d57a3; --bg-700: #4d699b; --bg-800: #43436c;
        --bg-900: #1f1f28; --bg-950: #000000;
        --primary-500: #5d57a3; --primary-600: #4d699b;
    }

    /* =========================================
       FAMILY: MODERN (Dracula / Nord / Rose)
       ========================================= */
    /* DRACULA (Dark) */
    [data-theme="dracula"] {
        --bg-50: #f8f8f2; --bg-100: #e2e2dc; --bg-200: #b9b9b5;
        --bg-300: #6272a4; --bg-400: #44475a; --bg-500: #282a36;
        --bg-600: #21222c; --bg-700: #191a21; --bg-800: #000000;
        --bg-900: #000000; --bg-950: #000000;
        --primary-500: #bd93f9; --primary-600: #ff79c6;
    }
    /* NORD (Dark) */
    [data-theme="nord-dark"] {
        --bg-50: #eceff4; --bg-100: #e5e9f0; --bg-200: #d8dee9;
        --bg-300: #4c566a; --bg-400: #434c5e; --bg-500: #3b4252;
        --bg-600: #2e3440; --bg-700: #242933; --bg-800: #1d212a;
        --bg-900: #15181f; --bg-950: #000000;
        --primary-500: #88c0d0; --primary-600: #5e81ac;
    }
    /* ROSÉ PINE (Dark) */
    [data-theme="rose-pine"] {
        --bg-50: #e0def4; --bg-100: #908caa; --bg-200: #6e6a86;
        --bg-300: #524f67; --bg-400: #403d52; --bg-500: #26233a;
        --bg-600: #1f1d2e; --bg-700: #191724; --bg-800: #110f19;
        --bg-900: #09080d; --bg-950: #000000;
        --primary-500: #eb6f92; --primary-600: #c4a7e7;
    }

    /* =========================================
       FAMILY: RETRO (Gruvbox / Solarized)
       ========================================= */
    /* GRUVBOX (Dark) */
    [data-theme="gruvbox-dark"] {
        --bg-50: #ebdbb2; --bg-100: #d5c4a1; --bg-200: #bdae93;
        --bg-300: #928374; --bg-400: #7c6f64; --bg-500: #504945;
        --bg-600: #3c3836; --bg-700: #282828; --bg-800: #1d2021;
        --bg-900: #141617; --bg-950: #000000;
        --primary-500: #fabd2f; --primary-600: #fb4934;
    }
    /* GRUVBOX (Light) */
    [data-theme="gruvbox-light"] {
        --bg-50: #fbf1c7; --bg-100: #ebdbb2; --bg-200: #d5c4a1;
        --bg-300: #bdae93; --bg-400: #928374; --bg-500: #7c6f64;
        --bg-600: #504945; --bg-700: #3c3836; --bg-800: #282828;
        --bg-900: #1d2021; --bg-950: #000000;
        --primary-500: #b57614; --primary-600: #9d0006;
    }

    /* =========================================
       FAMILY: UK AUTHENTIC (Heritage Brands)
       ========================================= */
    /* GOV.UK (Strict Light) */
    [data-theme="govuk"] {
        --bg-50: #ffffff; --bg-100: #f3f2f1; --bg-200: #e5e5e5;
        --bg-300: #b1b4b6; --bg-400: #6f777b; --bg-500: #505a5f;
        --bg-600: #383f43; --bg-700: #262a2c; --bg-800: #0b0c0c;
        --bg-900: #0b0c0c; --bg-950: #000000;
        --primary-500: #1d70b8; --primary-600: #003078;
    }
    /* FINANCIAL TIMES (Bisque Paper) */
    [data-theme="financial-times"] {
        --bg-50: #fff1e5; --bg-100: #f2dfce; --bg-200: #e9decf;
        --bg-300: #cec6b9; --bg-400: #a7a59b; --bg-500: #66605b;
        --bg-600: #4d4845; --bg-700: #33302e; --bg-800: #262220;
        --bg-900: #1a1716; --bg-950: #000000;
        --primary-500: #990f3d; --primary-600: #0d7680;
    }
    /* THE GUARDIAN (Navy & Yellow) */
    [data-theme="guardian"] {
        --bg-50: #ffffff; --bg-100: #f6f6f6; --bg-200: #ededed;
        --bg-300: #dcdcdc; --bg-400: #929292; --bg-500: #767676;
        --bg-600: #333333; --bg-700: #121212; --bg-800: #052962;
        --bg-900: #041f4a; --bg-950: #000000;
        --primary-500: #ffe500; --primary-600: #c70000;
    }
    /* NHS DIGITAL (Blue & White) */
    [data-theme="nhs"] {
        --bg-50: #ffffff; --bg-100: #f0f4f5; --bg-200: #d8dde0;
        --bg-300: #aeb7bd; --bg-400: #768692; --bg-500: #4c6272;
        --bg-600: #333333; --bg-700: #231f20; --bg-800: #005eb8;
        --bg-900: #003087; --bg-950: #002f5c;
        --primary-500: #005eb8; --primary-600: #007f3b;
    }
}

```

-----

### 2.4. **The JS Data Source**

**File:** `resources/js/theme-registry.js`

```javascript
export const themeRegistry = [
    // --- 1. Catppuccin Family ---
    { id: 'catppuccin-latte',     name: 'Catppuccin Latte',     family: 'catppuccin', mode: 'light' },
    { id: 'catppuccin-frappe',    name: 'Catppuccin Frappé',    family: 'catppuccin', mode: 'dark' },
    { id: 'catppuccin-macchiato', name: 'Catppuccin Macchiato', family: 'catppuccin', mode: 'dark' },
    { id: 'catppuccin-mocha',     name: 'Catppuccin Mocha',     family: 'catppuccin', mode: 'dark' },

    // --- 2. Tokyo Night ---
    { id: 'tokyo-night',          name: 'Tokyo Night',          family: 'asian',      mode: 'dark' },
    { id: 'tokyo-day',            name: 'Tokyo Day',            family: 'asian',      mode: 'light' },

    // --- 3. Kanagawa ---
    { id: 'kanagawa-wave',        name: 'Kanagawa Wave',        family: 'asian',      mode: 'dark' },
    { id: 'kanagawa-lotus',       name: 'Kanagawa Lotus',       family: 'asian',      mode: 'light' },

    // --- 4. Modern (Dracula / Nord / Rose) ---
    { id: 'dracula',              name: 'Dracula',              family: 'modern',     mode: 'dark' },
    { id: 'nord-dark',            name: 'Nord Dark',            family: 'modern',     mode: 'dark' },
    { id: 'rose-pine',            name: 'Rosé Pine',            family: 'modern',     mode: 'dark' },

    // --- 5. Retro (Gruvbox) ---
    { id: 'gruvbox-dark',         name: 'Gruvbox Dark',         family: 'retro',      mode: 'dark' },
    { id: 'gruvbox-light',        name: 'Gruvbox Light',        family: 'retro',      mode: 'light' },

    // --- 6. UK Authentic ---
    { id: 'govuk',                name: 'GOV.UK',               family: 'uk',         mode: 'light' },
    { id: 'financial-times',      name: 'Financial Times',      family: 'uk',         mode: 'light' },
    { id: 'guardian',             name: 'The Guardian',         family: 'uk',         mode: 'light' },
    { id: 'nhs',                  name: 'NHS Digital',          family: 'uk',         mode: 'light' },
    { id: 'tfl',                  name: 'TfL (Tube)',           family: 'uk',         mode: 'light' },
];

```

-----

### 2.5. **The Livewire Flux Switcher**

**File:** `resources/views/components/theme-switcher.blade.php`

```php
<?php
use Livewire\Volt\Component;
new class extends Component {
    // Optional: Sync with user profile on boot
    public string $currentTheme = 'catppuccin-mocha';
};
?>

<div x-data="{
    theme: localStorage.getItem('theme') || '{{ $currentTheme }}',

    // Import the registry (Inline here for simplicity, or import via JS bundle)
    registry: [
        { id: 'catppuccin-latte',     name: 'Latte',         family: 'catppuccin', mode: 'light' },
        { id: 'catppuccin-mocha',     name: 'Mocha',         family: 'catppuccin', mode: 'dark' },
        { id: 'tokyo-night',          name: 'Tokyo Night',   family: 'asian',      mode: 'dark' },
        { id: 'kanagawa-wave',        name: 'Kanagawa',      family: 'asian',      mode: 'dark' },
        { id: 'dracula',              name: 'Dracula',       family: 'modern',     mode: 'dark' },
        { id: 'nord-dark',            name: 'Nord',          family: 'modern',     mode: 'dark' },
        { id: 'gruvbox-dark',         name: 'Gruvbox',       family: 'retro',      mode: 'dark' },
        { id: 'govuk',                name: 'GOV.UK',        family: 'uk',         mode: 'light' },
        { id: 'financial-times',      name: 'Financial Times', family: 'uk',       mode: 'light' },
        { id: 'guardian',             name: 'Guardian',      family: 'uk',         mode: 'light' },
        { id: 'nhs',                  name: 'NHS',           family: 'uk',         mode: 'light' },
    ],

    setTheme(id) {
        this.theme = id;
        localStorage.setItem('theme', id);
        this.apply();
    },

    apply() {
        // 1. Find the definition
        const def = this.registry.find(t => t.id === this.theme);
        if (!def) return;

        // 2. Set the CSS Variables Scope
        document.documentElement.setAttribute('data-theme', this.theme);

        // 3. ENFORCED MODE: Toggle the Tailwind Dark Mode Class
        // This ensures Filament/Flux use the correct variables (50 vs 900)
        if (def.mode === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    },

    init() {
        this.apply();
    }
}" class="relative">

    <flux:dropdown position="bottom end">
        <flux:button icon="swatch" variant="subtle">Theme</flux:button>

        <flux:menu class="max-h-96 overflow-y-auto">

            <flux:menu.group heading="Catppuccin">
                <template x-for="t in registry.filter(r => r.family === 'catppuccin')">
                    <flux:menu.item x-text="t.name" x-on:click="setTheme(t.id)"></flux:menu.item>
                </template>
            </flux:menu.group>

            <flux:menu.separator />

            <flux:menu.group heading="Global / Developer">
                <template x-for="t in registry.filter(r => ['asian', 'modern', 'retro'].includes(r.family))">
                    <flux:menu.item x-text="t.name" x-on:click="setTheme(t.id)"></flux:menu.item>
                </template>
            </flux:menu.group>

            <flux:menu.separator />

            <flux:menu.group heading="UK Authentic">
                <template x-for="t in registry.filter(r => r.family === 'uk')">
                    <flux:menu.item x-text="t.name" x-on:click="setTheme(t.id)"></flux:menu.item>
                </template>
            </flux:menu.group>

        </flux:menu>
    </flux:dropdown>
</div>

```
