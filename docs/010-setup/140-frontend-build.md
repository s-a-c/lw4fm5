# Bun, Vite, and Tailwind CSS 4

Compliant with [AI-GUIDELINES.md](../../.ai/AI-GUIDELINES.md) v0921d4cfab198af1451ef177b6e47657b5d3ab0292f77bf232496291dee47183
<!-- markdownlint-disable MD013 -->

## 1 Introduction

This document covers the frontend build system including Bun package manager, Vite build tool, and Tailwind CSS 4 styling framework.

## 2 Bun Package Manager

### 2.1 Overview

**Package Manager**: Bun ^1.1.0
**Purpose**: Fast JavaScript runtime and package manager
**Architectural Role**: This project uses Bun exclusively. Do not use npm, npx, or yarn. Use `bun` and `bunx` instead.

### 2.2 Key Features

- Fast package installation (faster than npm/yarn)
- Native JavaScript runtime
- Built-in TypeScript support
- Fast test runner
- Built-in bundler

> [!IMPORTANT]
> This project uses Bun exclusively. Always use `bun` instead of `npm` and `bunx` instead of `npx`.

### 2.3 Installation Verification

``` bash
# Check Bun version
bun --version

# Bun version 1.1.0 or higher
```

### 2.4 Configuration Steps

| Step \# | Task/Configuration Item | Command/File/Code | Expected Result | Verification Method |
|----|----|----|----|----|
| 1 | Install Bun | Follow installation guide at <https://bun.sh> | Bun installed | `bun --version` |
| 2 | Verify installation | `bun --version` | Version displayed | Version ^1.1.0 shown |
| 3 | Install dependencies | `bun install` | Dependencies installed | `node_modules` directory exists |
| 4 | Run scripts | `bun run dev` or `bun run build` | Scripts execute | No errors |

### 2.5 Bun vs npm/npx

This project uses Bun exclusively. Here’s the equivalent command mapping:

| npm/npx Command          | Bun Equivalent            |
|--------------------------|---------------------------|
| `npm install`            | `bun install`             |
| `npm run <script>`       | `bun run <script>`        |
| `npm run dev`            | `bun run dev`             |
| `npm run build`          | `bun run build`           |
| `npx <package>`          | `bunx <package>`          |
| `npx playwright install` | `bunx playwright install` |
| `npm update`             | `bun update`              |

> [!WARNING]
> Do not use `npm`, `npx`, or `yarn` commands. They may cause issues with the project’s lock file (`bun.lock`) and dependency resolution.

### 2.6 Package.json Configuration

The project uses Bun as the package manager:

``` json
{
  "packageManager": "bun@latest",
  "engines": {
    "node": ">=25",
    "bun": ">=1.1.0"
  }
}
```

The `packageManager` field ensures Bun is used when running package commands.

**Source**: [Bun Installation Documentation](https://bun.sh/docs/cli/install)

## 3 Vite 7

### 3.1 Package Overview

**Package**: `vite` ^7.2.0
**Purpose**: Next-generation frontend build tool
**Architectural Role**: Bundles and optimizes frontend assets

### 3.2 Key Features

- Hot Module Replacement (HMR)
- Fast builds
- Optimized production bundles
- Code splitting
- Plugin ecosystem

**Source**: [Vite Guide](https://vitejs.dev/guide) \| [Vite Configuration](https://vitejs.dev/config)

### 3.3 Installation Verification

``` bash
# Check Vite version
bunx vite --version

# Or check in package.json
bun list vite
```

### 3.4 Configuration

The Vite configuration is in `vite.config.js`:

``` javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        cors: true,
    },
});
```

### 3.5 Configuration Steps

| Step \# | Task/Configuration Item | Command/File/Code | Expected Result | Verification Method |
|----|----|----|----|----|
| 1 | Verify Vite installation | `bun list vite` | Package version displayed | ^7.2.0 shown |
| 2 | Configure Vite | Edit `vite.config.js` | Configuration updated | Config file exists |
| 3 | Start dev server | `bun run dev` | Dev server running | Server responds on port 5173 |
| 4 | Build for production | `bun run build` | Assets built | `public/build` directory created |

### 3.6 Laravel Vite Plugin

**Package**: `laravel-vite-plugin` ^2.0.1
**Purpose**: Integrates Vite with Laravel
**Usage**: Automatically configured in `vite.config.js`

### 3.7 Rolldown Vite

**Package**: `rolldown-vite` ^7.2.0
**Purpose**: Fast Rust-based bundler for Vite
**Usage**: Automatically used by Vite

## 4 Tailwind CSS 4

### 4.1 Package Overview

**Package**: `tailwindcss` ^4.1.16
**Purpose**: Utility-first CSS framework
**Architectural Role**: Provides styling system for the application

### 4.2 Key Features

- Utility-first CSS
- Responsive design
- Dark mode support
- Customizable design system
- JIT (Just-In-Time) compilation

### 4.3 Tailwind CSS 4 Changes

Tailwind CSS 4 introduces significant changes from v3:

- **CSS-based configuration**: No `tailwind.config.js` file - this project uses pure CSS configuration
- **@import syntax**: Uses `@import "tailwindcss"` instead of `@tailwind` directives
- **New utilities**: Deprecated utilities replaced (see table below)

> [!IMPORTANT]
> This project uses **pure CSS configuration only**. Do not create a `tailwind.config.js` file. All customization should be done via CSS variables and `@theme` blocks in your CSS files.

**Source**: [Tailwind CSS v4 Alpha Documentation](https://tailwindcss.com/docs/v4-alpha)

### 4.4 Installation Verification

``` bash
# Check Tailwind version
bun list tailwindcss

# tailwindcss@^4.1.16
```

### 4.5 Configuration

Tailwind CSS 4 uses **pure CSS-based configuration** (no JavaScript config file). The main CSS file is `resources/css/app.css`:

``` css
@import "tailwindcss";

@theme {
    /* Custom theme variables here */
}
```

> [!NOTE]
> This project does not use `tailwind.config.js`. All configuration is done in CSS using `@theme` blocks and CSS variables.

### 4.6 Tailwind Vite Plugin

**Package**: `@tailwindcss/vite` ^4.1.16
**Purpose**: Integrates Tailwind CSS with Vite
**Usage**: Configured in `vite.config.js`

### 4.7 Deprecated Utilities

The following utilities are deprecated in Tailwind CSS 4:

| Deprecated        | Replacement     |
|-------------------|-----------------|
| bg-opacity-\*     | bg-black/\*     |
| text-opacity-\*   | text-black/\*   |
| border-opacity-\* | border-black/\* |
| flex-shrink-\*    | shrink-\*       |
| flex-grow-\*      | grow-\*         |
| overflow-ellipsis | text-ellipsis   |

### 4.8 Configuration Steps

| Step \# | Task/Configuration Item | Command/File/Code | Expected Result | Verification Method |
|----|----|----|----|----|
| 1 | Verify Tailwind installation | `bun list tailwindcss` | Package version displayed | ^4.1.16 shown |
| 2 | Configure CSS import | Ensure `@import "tailwindcss"` in CSS | CSS configured | Check `resources/css/app.css` |
| 3 | Include CSS in layout | Add `@vite(['resources/css/app.css'])` to Blade | Styles loaded | Check browser DevTools |
| 4 | Use Tailwind classes | Add utility classes to HTML | Styles applied | Visual verification |

## 5 Other Frontend Dependencies

### 5.1 Autoprefixer

**Package**: `autoprefixer` ^10.4.21
**Purpose**: Automatically adds vendor prefixes to CSS
**Usage**: Integrated with Vite automatically

### 5.2 Axios

**Package**: `axios` ^1.13.2
**Purpose**: HTTP client for JavaScript
**Usage**: Available for AJAX requests

### 5.3 Prettier

**Package**: `prettier` ^3.6.2
**Purpose**: Code formatter
**Plugins**:
\* `prettier-plugin-organize-imports` ^4.3.0
\* `prettier-plugin-tailwindcss` ^0.7.1
\* `prettier-plugin-packagejson` ^2.5.19

**Commands**:

``` bash
bun run lint          # Format code
bun run test:lint     # Check formatting
```

### 5.4 Testing Tools

- **Playwright** ^1.56.1 - Browser testing
- **Vitest** ^4.0.7 - Unit testing framework

### 5.5 Development Tools

- **Concurrently** ^9.2.1 - Run multiple commands
- **Bun Git Hooks** ^0.3.1 - Git hooks management
- **npm-check-updates** ^19.1.2 - Check for package updates (run with `bunx npm-check-updates`)

> [!NOTE]
> Even though `npm-check-updates` has "npm" in its name, it should be run with `bunx npm-check-updates`, not `npx`.

### 5.6 Optional Dependencies

Platform-specific binaries for:
\* Rollup (Linux, macOS ARM64, macOS x64)
\* Tailwind CSS Oxide (Linux, macOS ARM64, macOS x64)
\* Lightning CSS (Linux, macOS ARM64, macOS x64)

## 6 Build Scripts

The project includes the following package.json scripts (run with `bun run`):

> [!IMPORTANT]
> Always use `bun run <script>` instead of `npm run <script>`. Never use npm or npx.

``` json
{
  "scripts": {
    "build": "vite build",
    "dev": "vite",
    "preview": "vite preview",
    "lint": "prettier --write resources/",
    "test:lint": "prettier --check resources/",
    "playwright:install": "bunx playwright install --with-deps",
    "test:browser": "bunx playwright test"
  }
}
```

## 7 Environment Variables

No specific environment variables required for frontend build system.

## 8 Integration Points

### 8.1 With Laravel

- Vite assets are loaded via `@vite()` directive
- Laravel Vite plugin handles asset compilation
- Hot Module Replacement works with Laravel

### 8.2 With Livewire

- Livewire components can use Tailwind CSS classes
- Vite bundles Livewire assets
- Hot reloading works with Livewire updates

## 9 Troubleshooting

### 9.1 Assets Not Loading

**Symptom**: CSS/JS files return 404

**Solution**:

``` bash
# Rebuild assets
bun run build

# Or start dev server
bun run dev
```

### 9.2 Tailwind Classes Not Working

**Symptom**: Tailwind utilities not applying styles

**Solution**: Verify CSS import:

``` css
@import "tailwindcss";
```

### 9.3 Vite HMR Not Working

**Symptom**: Changes not reflected without refresh

**Solution**: Check Vite server is running:

``` bash
bun run dev
```

### 9.4 Using npm/npx Commands

**Symptom**: Lock file conflicts, dependency resolution issues, or `package-lock.json` created

**Solution**:
\* Never use `npm`, `npx`, or `yarn` commands in this project
\* Always use `bun` and `bunx` instead
\* If you accidentally used npm:
\* Delete `node_modules` directory
\* Delete `package-lock.json` if it was created
\* Delete `yarn.lock` if it was created
\* Run `bun install` to restore dependencies using `bun.lock`
\* The project uses `bun.lock` as the lock file - do not commit `package-lock.json` or `yarn.lock`

### 9.5 Gitignore Entries

The project’s `.gitignore` file includes entries for npm/yarn files (`npm-debug.log`, `yarn-error.log`) for compatibility, but these are not used since the project uses Bun exclusively.

**What to track:**
\* `bun.lock` - Bun lock file (should be committed)
\* `package.json` - Package definition (should be committed)

**What to ignore:**
\* `package-lock.json` - npm lock file (if accidentally created)
\* `yarn.lock` - Yarn lock file (if accidentally created)
\* `node_modules/` - Dependencies (ignored as usual)

> [!NOTE]
> If you accidentally use `npm` or `yarn` and create lock files, delete them and run `bun install` to restore the correct `bun.lock` file.

## 10 Next Steps

After configuring the frontend build system:

- [outstanding-questions.md](140-outstanding-questions.md) - Review outstanding questions and decisions

## 11 Navigation

[← Spec-Driven Development](125-spec-driven-development.md) | [↑ Top](#bun-vite-and-tailwind-css-4) | [Outstanding Questions, Decisions, and Inconsistencies →](140-outstanding-questions.md)
