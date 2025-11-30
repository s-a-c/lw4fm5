<!-- 78cce7b3-1b89-4aaf-b253-20e917f8158c 0ca99426-a50d-4554-9ae2-ad1ffe800643 -->
# Improve Theme Preview UI and Logic

## Problem Analysis
1. **Theme Count**: With 16 themes, the grid layout is cluttered.
2. **Light/Dark Mode**: Single-flavor light themes (e.g., GovUK) are incorrectly treated as dark because `isLight` logic only checks flavors, not themes.
3. **Accent Options**: Users want 4 accent choices (Theme + 3 alternatives) for all themes, but currently restricted to just 'Primary'.
4. **Naming**: 'Accent Color' header should be 'Primary Accent Color'.
5. **UX Polish**: Transitions for UI elements are abrupt; flavor selection just pops in/out, and buttons lack interactive feedback.

## Additional Issues Identified

### 3. Light-Only Themes Not Rendering Correctly
- **Problem**: Light-only themes (GovUK, TransportForLondon, NHS Digital, Financial Times, The Guardian) all look the same and no colors are being used in the preview component.
- **Root Cause**: CSS selectors in `resources/css/themes/all-themes.css` use `data-flavor='default'` but themes now use `data-flavor='light'`, so CSS rules don't match.
- **Solution**: Update CSS selectors from `[data-theme='gov-uk'][data-flavor='default']` to `[data-theme='gov-uk'][data-flavor='light']` for all light-only themes.

### 4. Dark-Only Themes Not Rendering Correctly
- **Problem**: Dark-only themes (Dracula, Nord, RosePine, OneDarkPro, MonokaiPro) may not be rendering correctly.
- **Root Cause**: CSS selectors use `data-flavor='default'` but themes now use `data-flavor='dark'`.
- **Solution**: Update CSS selectors from `[data-theme='dracula'][data-flavor='default']` to `[data-theme='dracula'][data-flavor='dark']` for all dark-only themes.

### 5. Accent Color Buttons Need Visual Demonstration
- **Problem**: Accent color buttons should show/demonstrate the effect of their associated color as an accent.
- **Current State**: Preview component already has accent cards with preview elements, but they may not be using the correct CSS variables or may need enhancement.
- **Solution**:
  - Ensure accent preview elements use theme-specific CSS variables (e.g., `var(--accent-primary)`, `var(--accent-blue)`)
  - Use `var(--accent-content)` for text color on accent-colored backgrounds
  - Ensure preview elements are visible and styled correctly

### 6. Confusion Between Light Flavor and isLight()
- **Problem**: There's confusion in code between "Light flavor" and `isLight()` method.
- **Clarification Needed**:
  - **Light flavor** (`ThemeFlavor::Light`) should:
    1. Set colors according to the theme palette (via CSS `data-flavor='light'` selector)
    2. Trigger `isLight()` to return `true` (remove `dark` class from HTML)
  - **Preview and sidebar** should respect the color palette associated with the theme
- **Solution**:
  - Ensure `ThemeData::isLight()` correctly identifies Light flavor
  - Ensure CSS has proper selectors for `[data-theme='...'][data-flavor='light']`
  - Ensure JavaScript applies/removes `dark` class based on `isLight()` value
  - Ensure preview component and sidebar use theme-specific CSS variables

## Proposed Changes

### 1. Logic Updates (Light/Dark & Accents)
- **`app/Data/ThemeData.php`**: Update `isLight()` to return `true` for light-only themes (`GovUk`, `TransportForLondon`, `NhsDigital`, `FinancialTimes`, `TheGuardian`), in addition to light flavors.
- **`app/Services/Theme/ThemeAccentMapper.php`**: Revert to returning all 4 accents (`Primary`, `Blue`, `Red`, `Green`) for all themes (except 'None').
- **`resources/views/livewire/themes/preview.blade.php`**:
    - Calculate `isLight` state in `updateSessionAndDom`.
    - Pass `isLight` to the frontend via `window.__liveThemePreview` and `theme-updated` event.
- **`resources/js/app.js`**: Update `applyThemeToDom` to accept and apply the `isLight` property directly from the server response, ensuring correct mode switching.

### 2. UI Redesign (`preview.blade.php`)
- **Theme Selection**: Convert the large grid into a **Dropdown/Select** menu for cleaner selection of the 16 themes.
- **Flavor Selection**:
    - Keep as **Radio Cards**, but only display this section if the selected theme has multiple flavors.
    - **Animation**: Wrap the flavor section in a container with transition classes (e.g., `transition-all duration-300 ease-in-out`) to animate height/opacity when revealing/hiding.
- **Accent Selection**:
    - Rename header to **"Accent Colors"**.
    - **Demonstration UI**: Display a grid of "Accent Cards" for **Theme (Primary)**, **Blue**, **Red**, and **Green**.
    - Each card acts as a selection radio button and includes:
        - **Preview Element**:
            - **Theme (Primary)**: Sample **Button** using `var(--accent-primary)`.
            - **Blue**: Sample **Info Badge** or **Link** using `var(--accent-blue)`.
            - **Red**: Sample **Error Icon/Text** using `var(--accent-red)`.
            - **Green**: Sample **Success Toggle/Check** using `var(--accent-green)`.
        - **Label**: The accent name.
        - **Description**: Purpose (e.g., "Brand/Default", "Information", "Destructive/Error", "Success").
    - **Animation**: Add hover and selection animations to cards (e.g., `hover:scale-105`, `active:scale-95`, `transition-transform`) for better tactile feel.

### 3. Fix CSS Selectors for Light-Only Themes
- **`resources/css/themes/all-themes.css`**: Update all light-only theme selectors from `data-flavor='default'` to `data-flavor='light'`:
  - `[data-theme='gov-uk'][data-flavor='default']` → `[data-theme='gov-uk'][data-flavor='light']`
  - `[data-theme='transport-for-london'][data-flavor='default']` → `[data-theme='transport-for-london'][data-flavor='light']`
  - `[data-theme='nhs-digital'][data-flavor='default']` → `[data-theme='nhs-digital'][data-flavor='light']`
  - `[data-theme='financial-times'][data-flavor='default']` → `[data-theme='financial-times'][data-flavor='light']`
  - `[data-theme='the-guardian'][data-flavor='default']` → `[data-theme='the-guardian'][data-flavor='light']`

### 4. Fix CSS Selectors for Dark-Only Themes
- **`resources/css/themes/all-themes.css`**: Update all dark-only theme selectors from `data-flavor='default'` to `data-flavor='dark'`:
  - `[data-theme='dracula'][data-flavor='default']` → `[data-theme='dracula'][data-flavor='dark']`
  - `[data-theme='nord'][data-flavor='default']` → `[data-theme='nord'][data-flavor='dark']`
  - `[data-theme='rose-pine'][data-flavor='default']` → `[data-theme='rose-pine'][data-flavor='dark']`
  - `[data-theme='one-dark-pro'][data-flavor='default']` → `[data-theme='one-dark-pro'][data-flavor='dark']`
  - `[data-theme='monokai-pro'][data-flavor='default']` → `[data-theme='monokai-pro'][data-flavor='dark']`

### 5. Add Light Mode Overrides for Light-Only Themes
- **`resources/css/themes/all-themes.css`**: Add light-only themes to all light mode override selectors:
  - Background color overrides (use `var(--color-zinc-50)` for background, `var(--color-zinc-950)` for text)
  - Text color overrides for utility classes (`.text-gray-50`, `.text-zinc-50`, etc.)
  - Heading and label color overrides
  - Dark variant overrides

### 6. Enhance Accent Color Demonstration
- **`resources/views/livewire/themes/preview.blade.php`**:
  - Verify accent preview elements are using correct CSS variables
  - Use `var(--accent-content)` for text color on accent-colored backgrounds
  - Ensure preview elements are visible and styled correctly
  - Add hover effects that demonstrate the accent color more prominently
  - Ensure selected accent card shows the accent color in its border/background

## Verification
- Select 'GovUK' -> Should switch to Light mode automatically and show GovUK color palette (blue/white).
- Select 'TransportForLondon' -> Should show TransportForLondon color palette (blue tones).
- Select 'NHS Digital' -> Should show NHS Digital color palette (blue/white).
- Select 'Financial Times' -> Should show Financial Times color palette (pink/salmon tones).
- Select 'The Guardian' -> Should show The Guardian color palette (blue/yellow).
- Select 'Dracula' -> Should show Dracula color palette (purple/dark).
- Select 'Nord' -> Should show Nord color palette (blue/cold tones).
- Select 'Catppuccin' -> Select 'Latte' flavor -> Should switch to Light mode and show Latte colors.
- Select 'Kanagawa' -> Select 'Wave' flavor -> Should switch to Dark mode and show Wave colors.
- Verify 4 accent options are available and apply correctly.
- Verify accent buttons show correct preview elements with theme-specific accent colors.
- Verify accent buttons demonstrate their colors (button, badge, error, success elements).
- Verify flavor section reveals smoothly when switching from a single-flavor theme to a multi-flavor theme.
- Verify accent buttons animate on hover/click and show selected state with accent color.

### To-dos

- [x] Add all 15 themes to Theme enum (catppuccin, kanagawa, tokyo-night, dracula, nord, rose-pine, one-dark-pro, monokai-pro, gruvbox, solarized, gov-uk, transport-for-london, nhs-digital, financial-times, the-guardian)
- [x] Add 'None' option to Theme enum for system default
- [x] Add missing flavors to ThemeFlavor enum (tokyo-night day/night, gruvbox dark/light, solarized dark/light, default flavors for single-flavor themes)
- [x] Update Theme::flavors() method to return correct flavors for all 15 themes
- [x] Update ThemeAccentMapper to return only Primary accent for all themes
- [x] Update preview page to hide flavor selection when theme has only one flavor
- [x] Handle 'None' theme option (system default - no data-theme attribute)
- [x] Update ThemeService to handle all new themes and None option
- [x] Fix CSS selectors for light-only themes (gov-uk, transport-for-london, nhs-digital, financial-times, the-guardian) - change from `data-flavor='default'` to `data-flavor='light'`
- [x] Fix CSS selectors for dark-only themes (dracula, nord, rose-pine, one-dark-pro, monokai-pro) - change from `data-flavor='default'` to `data-flavor='dark'`
- [x] Add light mode overrides for light-only themes in CSS
- [ ] Verify accent buttons demonstrate their colors correctly using theme-specific CSS variables
- [ ] Verify preview component and sidebar respect theme color palettes
- [ ] Test all light-only themes render with correct colors
- [ ] Test all dark-only themes render with correct colors
