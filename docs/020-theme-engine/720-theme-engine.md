# Architecture Document: Filament/Flux Multi-Theme Engine

-----

<details>
<summary>Expand for Table of Contents</summary>

- [Architecture Document: Filament/Flux Multi-Theme Engine](#architecture-document-filamentflux-multi-theme-engine)
  - [1. Project Overview \& Requirements](#1-project-overview--requirements)
    - [1.1. Technology Stack](#11-technology-stack)
    - [1.2. Assumptions](#12-assumptions)
  - [2. Architecture: "The Zinc Bridge"](#2-architecture-the-zinc-bridge)
  - [3. Domain Modeling (TDD Started)](#3-domain-modeling-tdd-started)
    - [3.1. Enums](#31-enums)
    - [3.2. User Settings DTO](#32-user-settings-dto)
    - [3.3. Test Coverage (Enums \& DTO)](#33-test-coverage-enums--dto)
  - [4. Database Implementation](#4-database-implementation)
    - [4.1. Migration](#41-migration)
    - [4.2. User Model](#42-user-model)
  - [5. The CSS Engine (Tailwind v4)](#5-the-css-engine-tailwind-v4)
  - [6. Backend Integration (Middleware)](#6-backend-integration-middleware)
    - [6.1. Integration Testing (Middleware)](#61-integration-testing-middleware)
  - [7. Frontend Logic (Livewire v4 Native SFC)](#7-frontend-logic-livewire-v4-native-sfc)
    - [7.1. Component Testing](#71-component-testing)
  - [8. PHPStan Configuration](#8-phpstan-configuration)

</details>

-----

## 1. Project Overview & Requirements

**Objective:** Create a scalable, user-selectable theming system supporting deep customization (Theme Family + Variant + Accent Color). The system must allow instant client-side preview and server-side persistence.

### 1.1. Technology Stack

  * **Framework:** Laravel 11+ (PHP 8.2+)
  * **Admin Panel:** Filament v4/v5 (Bleeding Edge)
  * **Frontend UI:** Livewire v4 (Beta), Flux UI
  * **CSS Engine:** Tailwind CSS v4
  * **Data Handling:** `spatie/laravel-data`
  * **Testing:** Pest (TDD), PHPStan (Level 9)

### 1.2. Assumptions

1. **Filament v4/v5** generates CSS using Tailwind v4 `@source` detection.
2. **Flux UI** relies on the `zinc` color palette by default.
3. **Filament** relies on the `gray` color palette by default.
4. **Livewire v4** is installed and configured to use native Single File Components (SFC).

-----

## 2. Architecture: "The Zinc Bridge"

To avoid maintaining duplicate color palettes for different libraries, we implement a "Zinc Bridge":

1. **Normalization:** We configure Tailwind to alias Filament's `gray` palette to Flux's `zinc` palette.
2. **Injection:** We use CSS compound selectors (e.g., `[data-theme="catppuccin"][data-flavor="mocha"]`) to override the `zinc` palette variables with specific theme hex codes.
3. **Result:** When the theme changes, both Flux (UI Kit) and Filament (Admin Panel) update simultaneously.

-----

## 3. Domain Modeling (TDD Started)

We begin with the Enums and Data Transfer Objects (DTOs) to enforce strict typing.

### 3.1. Enums

**File:** `app/Enums/Theme.php`

```php
<?php

namespace App\Enums;

enum Theme: string
{
    case Catppuccin = 'catppuccin';
    case Kanagawa = 'kanagawa';

    public function flavors(): array
    {
        return match($this) {
            self::Catppuccin => [ThemeFlavor::Latte, ThemeFlavor::Mocha],
            self::Kanagawa => [ThemeFlavor::Wave, ThemeFlavor::Dragon],
        };
    }
}

```

**File:** `app/Enums/ThemeFlavor.php`

```php
<?php

namespace App\Enums;

enum ThemeFlavor: string
{
    case Latte = 'latte';
    case Mocha = 'mocha';
    case Wave = 'wave';
    case Dragon = 'dragon';

    public function isLight(): bool
    {
        return $this === self::Latte;
    }
}

```

**File:** `app/Enums/ThemeAccent.php`

```php
<?php

namespace App\Enums;

enum ThemeAccent: string
{
    case Primary = 'primary';
    case Red = 'red';
    case Blue = 'blue';
}

```

### 3.2. User Settings DTO

**File:** `app/Data/UserSettingsData.php`

```php
<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use App\Enums\Theme;
use App\Enums\ThemeFlavor;
use App\Enums\ThemeAccent;

class UserSettingsData extends Data
{
    public function __construct(
        public Theme $theme = Theme::Catppuccin,
        public ThemeFlavor $flavor = ThemeFlavor::Mocha,
        public ThemeAccent $accent = ThemeAccent::Primary,
    ) {}
}

```

### 3.3. Test Coverage (Enums & DTO)

**File:** `tests/Unit/ThemeTest.php`

```php
<?php

use App\Enums\Theme;
use App\Enums\ThemeFlavor;
use App\Data\UserSettingsData;

test('theme returns correct flavors', function () {
    expect(Theme::Catppuccin->flavors())
        ->toContain(ThemeFlavor::Mocha)
        ->not->toContain(ThemeFlavor::Wave);
});

test('flavor identifies light mode', function () {
    expect(ThemeFlavor::Latte->isLight())->toBeTrue()
        ->and(ThemeFlavor::Mocha->isLight())->toBeFalse();
});

test('settings dto has defaults', function () {
    $data = new UserSettingsData();
    expect($data->theme)->toBe(Theme::Catppuccin)
        ->and($data->flavor)->toBe(ThemeFlavor::Mocha);
});

```

-----

## 4. Database Implementation

### 4.1. Migration

**Command:** `php artisan make:migration add_settings_to_users_table`

```php
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->json('settings')->nullable();
    });
}

```

### 4.2. User Model

**File:** `app/Models/User.php`

```php
<?php

namespace App\Models;

use App\Data\UserSettingsData;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\LaravelData\WithData;

class User extends Authenticatable
{
    use WithData;

    protected $casts = [
        'settings' => UserSettingsData::class,
    ];

    // Ensure settings object is never null when accessed
    protected static function booted(): void
    {
        static::retrieved(function (User $user) {
            if ($user->settings === null) {
                $user->settings = new UserSettingsData();
            }
        });
    }
}

```

-----

## 5. The CSS Engine (Tailwind v4)

**File:** `resources/css/app.css`

```css
@import "tailwindcss";
@import "../../vendor/livewire/flux/dist/flux.css";

/* Sources */
@source "../views";
@source "../../vendor/filament/**/*.blade.php";

@theme {
    /* 1. THE BRIDGE: Map Filament (Gray) to Flux (Zinc) */
    --color-gray-50: var(--color-zinc-50);
    --color-gray-100: var(--color-zinc-100);
    --color-gray-900: var(--color-zinc-900);
    --color-gray-950: var(--color-zinc-950);
    /* ... Map all 50-950 ... */

    /* 2. Generic Accent Var */
    --color-accent: var(--color-zinc-900);
}

@layer theme {
    /* 3. Theme Defintions */

    /* Catppuccin Mocha */
    [data-theme="catppuccin"][data-flavor="mocha"] {
        --color-zinc-50: #cdd6f4;
        --color-zinc-900: #1e1e2e; /* Base */
        --color-zinc-950: #11111b; /* Crust */
        --accent-blue: #89b4fa;
        --accent-red: #f38ba8;
    }

    /* Kanagawa Wave */
    [data-theme="kanagawa"][data-flavor="wave"] {
        --color-zinc-50: #dcd7ba;
        --color-zinc-900: #1f1f28;
        --color-zinc-950: #16161d;
        --accent-blue: #7fb4ca;
        --accent-red: #c34043;
    }

    /* 4. Accent Mapping */
    [data-accent="blue"] { --color-accent: var(--accent-blue); }
    [data-accent="red"]  { --color-accent: var(--accent-red); }
}

```

-----

## 6. Backend Integration (Middleware)

We need a helper class for Filament PHP colors, then the middleware to inject them.

**File:** `app/Support/ThemeColorHelper.php`

```php
<?php

namespace App\Support;

use App\Enums\ThemeAccent;
use Filament\Support\Colors\Color;

class ThemeColorHelper
{
    public static function getFilamentColors(ThemeAccent $accent): array
    {
        // Simple mapping for PHP-side semantic colors (rings, focus states)
        // Hex values should approximate the specific theme, or use a safe generic.
        $hex = match($accent) {
            ThemeAccent::Blue => '#89b4fa',
            ThemeAccent::Red => '#f38ba8',
            default => '#cba6f7',
        };

        return ['primary' => Color::hex($hex)];
    }
}

```

**File:** `app/Http/Middleware/ApplyTheme.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Blade;
use App\Data\UserSettingsData;
use App\Support\ThemeColorHelper;

class ApplyTheme
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()) {
            /** @var \App\Models\User $user */
            $user = auth()->user();
            // Handle null case explicitly for PHPStan
            $settings = $user->settings ?? new UserSettingsData();

            // 1. Register PHP Colors
            FilamentColor::register(ThemeColorHelper::getFilamentColors($settings->accent));

            // 2. Inject JS/CSS Hooks
            FilamentView::registerRenderHook(
                'panels::html.start',
                fn () => Blade::render(<<<'HTML'
                    <script>
                        const r = document.documentElement;
                        r.dataset.theme = @js($theme);
                        r.dataset.flavor = @js($flavor);
                        r.dataset.accent = @js($accent);
                        r.classList.toggle('dark', @js(!$isLight));
                    </script>
                HTML, [
                    'theme' => $settings->theme->value,
                    'flavor' => $settings->flavor->value,
                    'accent' => $settings->accent->value,
                    'isLight' => $settings->flavor->isLight(),
                ])
            );
        }

        return $next($request);
    }
}

```

### 6.1. Integration Testing (Middleware)

**File:** `tests/Feature/ThemeMiddlewareTest.php`

```php
<?php

use App\Models\User;
use App\Enums\Theme;
use App\Enums\ThemeFlavor;
use App\Data\UserSettingsData;
use App\Http\Middleware\ApplyTheme;
use Illuminate\Support\Facades\Route;

test('middleware injects theme attributes into response', function () {
    $user = User::factory()->create([
        'settings' => new UserSettingsData(
            theme: Theme::Kanagawa,
            flavor: ThemeFlavor::Wave
        )
    ]);

    Route::get('/test-theme', fn () => 'content')->middleware([ApplyTheme::class]);

    $this->actingAs($user)
        ->get('/test-theme')
        ->assertOk()
        ->assertSee('dataset.theme = \'kanagawa\'', false);
});

```

-----

## 7. Frontend Logic (Livewire v4 Native SFC)

**File:** `resources/views/livewire/settings/appearance.blade.php`

```php
<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Data\UserSettingsData;
use App\Enums\Theme;
use App\Enums\ThemeFlavor;
use App\Enums\ThemeAccent;

new #[Layout('components.layouts.app')] class extends Component {
    public string $theme;
    public string $flavor;
    public string $accent;
    public array $availableFlavors = [];

    public function mount(): void
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $settings = $user->settings ?? new UserSettingsData();

        $this->theme = $settings->theme->value;
        $this->flavor = $settings->flavor->value;
        $this->accent = $settings->accent->value;
        $this->updateAvailableFlavors();
    }

    public function updateAvailableFlavors(): void
    {
        $this->availableFlavors = Theme::from($this->theme)->flavors();

        // Reset flavor if invalid for new theme
        $current = ThemeFlavor::tryFrom($this->flavor);
        if (!in_array($current, $this->availableFlavors)) {
            $this->flavor = $this->availableFlavors[0]->value;
            $this->updated('flavor', $this->flavor); // Trigger save/js
        }
    }

    public function updated(string $property, mixed $value): void
    {
        if ($property === 'theme') $this->updateAvailableFlavors();

        /** @var \App\Models\User $user */
        $user = auth()->user();
        $settings = $user->settings ?? new UserSettingsData();

        if ($property === 'theme') $settings->theme = Theme::from($value);
        if ($property === 'flavor') $settings->flavor = ThemeFlavor::from($value);
        if ($property === 'accent') $settings->accent = ThemeAccent::from($value);

        $user->settings = $settings;
        $user->save();

        // Instant JS Injection
        $this->js(<<<'JS'
            const r = document.documentElement;
            r.dataset.theme = $wire.theme;
            r.dataset.flavor = $wire.flavor;
            r.dataset.accent = $wire.accent;
            // Toggle dark class logic here...
        JS);
    }
};
?>

<div class="space-y-6">
    <flux:fieldset>
        <flux:legend>Theme</flux:legend>
        <div class="grid grid-cols-2 gap-4">
            @foreach(Theme::cases() as $t)
                <flux:radio wire:model.live="theme" :value="$t->value" :label="$t->name" />
            @endforeach
        </div>
    </flux:fieldset>

    <flux:fieldset>
        <flux:legend>Flavor</flux:legend>
        <div class="grid grid-cols-4 gap-4">
            @foreach($availableFlavors as $f)
                <flux:radio wire:model.live="flavor" :value="$f->value" :label="$f->name" />
            @endforeach
        </div>
    </flux:fieldset>
</div>

```

### 7.1. Component Testing

**File:** `tests/Feature/AppearanceSettingsTest.php`

```php
<?php

use App\Models\User;
use App\Enums\Theme;
use Livewire\Livewire;

test('user can switch themes', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('settings.appearance')
        ->set('theme', Theme::Kanagawa->value)
        ->assertSet('theme', Theme::Kanagawa->value);

    $user->refresh();
    expect($user->settings->theme)->toBe(Theme::Kanagawa);
});

```

-----

## 8. PHPStan Configuration

To satisfy "Maximum Strictness", create/update `phpstan.neon`.

```neon
includes:
    - ./vendor/larastan/larastan/extension.neon

parameters:
    paths:
        - app

    # The highest level
    level: 9

    ignoreErrors:
        # Ignore generated Livewire view files if necessary
        - '#.*resources/views/livewire.*#'

```

**Key Code Adjustments for Level 9:**

1. **Type Hinting:** Use `/** @var \App\Models\User $user */` when pulling from `auth()->user()`.
2. **Null Handling:** Explicitly handle `$user->settings ?? new UserSettingsData()` everywhere, or ensure the Model accessor is strictly typed to never return null.
3. **Enum Casting:** Use `Theme::from($value)` inside setters to ensure string inputs are validated against the Enum backing values.

-----
