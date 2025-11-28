<?php

declare(strict_types=1);

use App\Contracts\ThemeAccentMapperInterface;
use App\Data\ThemeData;
use App\Data\UserSettingsData;
use App\Enums\Theme;
use App\Enums\ThemeAccent;
use App\Enums\ThemeFlavor;
use App\Services\Theme\ThemeService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Js;
use Livewire\Component;

new class extends Component
{
    public string $theme = '';

    public string $flavor = '';

    public string $accent = '';

    /** @var array<int, ThemeFlavor> */
    public array $availableFlavors = [];

    /** @var array<int, ThemeAccent> */
    public array $availableAccents = [];

    private ThemeService $themeService;

    private ThemeAccentMapperInterface $accentMapper;

    public function boot(
        ThemeService $themeService,
        ThemeAccentMapperInterface $accentMapper,
    ): void {
        $this->themeService = $themeService;
        $this->accentMapper = $accentMapper;
    }

    public function mount(?string $theme = null, ?string $flavor = null, ?string $accent = null): void
    {
        // Simple test to verify mount() is called
        error_log('MOUNT CALLED - theme param: '.($theme ?? 'null').', flavor param: '.($flavor ?? 'null'));

        // Get theme data from query parameters (highest priority for HTTP GET), function parameters, session, or defaults
        $request = request();

        // DEBUG: Inspect the actual request object
        $queryTheme = $request->query('theme');
        $queryFlavor = $request->query('flavor');
        $queryAccent = $request->query('accent');

        error_log("MOUNT - query('theme'): ".($queryTheme ?? 'null'));
        error_log("MOUNT - query('flavor'): ".($queryFlavor ?? 'null'));
        error_log("MOUNT - query('accent'): ".($queryAccent ?? 'null'));
        error_log('MOUNT - fullUrl(): '.$request->fullUrl());

        $debugInfo = [
            'request_method' => $request->method(),
            'request_url' => $request->fullUrl(),
            'request_path' => $request->path(),
            'request_query_all' => $request->query(),
            'query_theme' => $queryTheme,
            'query_flavor' => $queryFlavor,
            'query_accent' => $queryAccent,
            'param_theme' => $theme,
            'param_flavor' => $flavor,
            'param_accent' => $accent,
            'session_theme' => Session::get('preview_theme'),
            'session_flavor' => Session::get('preview_flavor'),
            'session_accent' => Session::get('preview_accent'),
        ];
        file_put_contents(storage_path('logs/theme-preview-debug.log'), "=== MOUNT START ===\n".json_encode($debugInfo, JSON_PRETTY_PRINT)."\n", FILE_APPEND);

        // ALWAYS prioritize query parameters first for HTTP GET requests
        // This ensures fresh page loads with query params work correctly
        $rawTheme = null;
        $rawFlavor = null;
        $rawAccent = null;

        // Check query parameters first (for HTTP GET requests)
        // Always check query parameters directly - don't rely on has() which might fail
        $queryTheme = $request->query('theme');
        $queryFlavor = $request->query('flavor');
        $queryAccent = $request->query('accent');

        if ($queryTheme !== null || $queryFlavor !== null || $queryAccent !== null) {
            $rawTheme = $queryTheme;
            $rawFlavor = $queryFlavor;
            $rawAccent = $queryAccent;

            // Clear session data when query parameters are present to ensure fresh state
            Session::forget(['preview_theme', 'preview_flavor', 'preview_accent']);
        } else {
            // Fall back to function parameters (for Livewire::test()), then session, then defaults
            $rawTheme = $theme ?? Session::get('preview_theme');
            $rawFlavor = $flavor ?? Session::get('preview_flavor');
            $rawAccent = $accent ?? Session::get('preview_accent');
        }

        // DEBUG: Show what we decided to use
        $finalInfo = [
            'raw_theme' => $rawTheme,
            'raw_flavor' => $rawFlavor,
            'raw_accent' => $rawAccent,
        ];
        file_put_contents(storage_path('logs/theme-preview-debug.log'), "FINAL VALUES:\n".json_encode($finalInfo, JSON_PRETTY_PRINT)."\n=== MOUNT END ===\n\n", FILE_APPEND);

        // DEBUG: Log what we received
        if (app()->environment('testing')) {
            \Log::info('ThemePreview mount() START', [
                'url' => $request->fullUrl(),
                'query_theme' => $request->query('theme'),
                'query_flavor' => $request->query('flavor'),
                'query_accent' => $request->query('accent'),
                'get_theme' => $request->get('theme'),
                'get_flavor' => $request->get('flavor'),
                'get_accent' => $request->get('accent'),
                'param_theme' => $theme,
                'param_flavor' => $flavor,
                'param_accent' => $accent,
                'session_theme' => Session::get('preview_theme'),
                'session_flavor' => Session::get('preview_flavor'),
                'session_accent' => Session::get('preview_accent'),
                'raw_theme' => $rawTheme,
                'raw_flavor' => $rawFlavor,
                'raw_accent' => $rawAccent,
            ]);
        }

        // Resolve theme data from raw values or use defaults
        $themeData = null;

        // Special handling for 'none' theme - it doesn't require valid flavor/accent
        if ($rawTheme === 'none') {
            $themeData = $this->themeService->resolveThemeData(
                new UserSettingsData(
                    theme: Theme::None,
                    flavor: ThemeFlavor::Default, // Placeholder
                    accent: ThemeAccent::Primary, // Placeholder
                )
            );
        } elseif ($rawTheme && $rawFlavor && $rawAccent) {
            $themeEnum = Theme::tryFrom($rawTheme);
            $flavorEnum = ThemeFlavor::tryFrom($rawFlavor);
            $accentEnum = ThemeAccent::tryFrom($rawAccent);

            if ($themeEnum && $flavorEnum && $accentEnum) {
                $themeData = $this->themeService->resolveThemeData(
                    new UserSettingsData(
                        theme: $themeEnum,
                        flavor: $flavorEnum,
                        accent: $accentEnum,
                    )
                );
            }
        }

        // Use defaults if raw values are empty or invalid
        if (! $themeData) {
            $themeData = $this->themeService->resolveThemeData(null);
        }

        // Set component properties from resolved theme data
        $this->theme = $themeData->theme->value;
        error_log('MOUNT - Set this->theme to: '.$this->theme);
        // For None theme, use placeholder values (not used but required for component state)
        $this->flavor = $themeData->theme === Theme::None ? 'default' : $themeData->flavor->value;
        $this->accent = $themeData->theme === Theme::None ? 'primary' : $themeData->accent->value;
        error_log('MOUNT - Set this->flavor to: '.$this->flavor.', this->accent to: '.$this->accent);

        // CRITICAL: Refresh available options IMMEDIATELY after setting theme
        // This ensures availableFlavors and availableAccents match the current theme
        $this->refreshAvailableOptions();

        // Validate and correct flavor/accent after refreshing options (skip for 'none' theme)
        if ($this->theme !== 'none') {
            $flavorValues = array_map(fn ($f) => $f->value, $this->availableFlavors);
            if (! in_array($this->flavor, $flavorValues, true) && ! empty($flavorValues)) {
                $this->flavor = $flavorValues[0];
            }

            $accentValues = array_map(fn ($a) => $a->value, $this->availableAccents);
            if (! in_array($this->accent, $accentValues, true) && ! empty($accentValues)) {
                $this->accent = $accentValues[0];
            }
        }

        // Save to session
        Session::put([
            'preview_theme' => $this->theme,
            'preview_flavor' => $this->flavor,
            'preview_accent' => $this->accent,
        ]);

        // DEBUG: Log final state
        if (app()->environment('testing')) {
            \Log::info('ThemePreview mount() END', [
                'final_theme' => $this->theme,
                'final_flavor' => $this->flavor,
                'final_accent' => $this->accent,
                'available_flavors_count' => count($this->availableFlavors),
                'available_flavors' => array_map(fn ($f) => $f->value, $this->availableFlavors),
            ]);
        }
    }

    public function updatedTheme(string $value): void
    {
        // Validate and correct theme value
        $this->theme = $this->safeThemeFromValue($value)->value;
        $this->refreshAvailableOptions();
        $this->updateSessionAndDom();
    }

    public function updatedFlavor(string $value): void
    {
        $this->flavor = $this->valueWithinFlavors($value);
        $this->updateSessionAndDom();
    }

    public function updatedAccent(string $value): void
    {
        $this->accent = $this->valueWithinAccents($value);
        $this->updateSessionAndDom();
    }

    public function render(): Illuminate\Contracts\View\View
    {
        // CRITICAL: ALWAYS prioritize query parameters for HTTP GET requests
        $request = request();
        $queryTheme = $request->query('theme');

        // If query param 'theme' is present, force-set component state immediately
        if ($queryTheme !== null) {
            $this->theme = $queryTheme;
            $this->refreshAvailableOptions();
            // Set flavor/accent from query params or use placeholders for 'none'
            $queryFlavor = $request->query('flavor');
            $queryAccent = $request->query('accent');
            if ($this->theme === 'none') {
                $this->flavor = 'default';
                $this->accent = 'primary';
            } else {
                $this->flavor = $queryFlavor ?? $this->flavor;
                $this->accent = $queryAccent ?? $this->accent;
            }
        }

        // Try multiple methods to get query parameters
        $queryTheme = $request->query('theme') ?? $request->get('theme') ?? $request->input('theme');
        $queryFlavor = $request->query('flavor') ?? $request->get('flavor') ?? $request->input('flavor');
        $queryAccent = $request->query('accent') ?? $request->get('accent') ?? $request->input('accent');

        // Also try parsing the URL directly as a fallback
        if ($queryTheme === null) {
            $url = $request->fullUrl();
            if (preg_match('/[?&]theme=([^&]+)/', $url, $matches)) {
                $queryTheme = urldecode($matches[1]);
            }
        }
        if ($queryFlavor === null) {
            $url = $request->fullUrl();
            if (preg_match('/[?&]flavor=([^&]+)/', $url, $matches)) {
                $queryFlavor = urldecode($matches[1]);
            }
        }
        if ($queryAccent === null) {
            $url = $request->fullUrl();
            if (preg_match('/[?&]accent=([^&]+)/', $url, $matches)) {
                $queryAccent = urldecode($matches[1]);
            }
        }

        // If query parameters are present, process them IMMEDIATELY and update component state
        // Do this BEFORE any other component property access
        // DEBUG: Log query parameter detection
        file_put_contents(storage_path('logs/theme-preview-sequence.log'),
            "=== RENDER START ===\n".
            'queryTheme: '.($queryTheme ?? 'null')."\n".
            'queryFlavor: '.($queryFlavor ?? 'null')."\n".
            'queryAccent: '.($queryAccent ?? 'null')."\n".
            'current component theme: '.($this->theme ?? 'null')."\n".
            'current availableFlavors: '.json_encode(array_map(fn ($f) => $f->value, $this->availableFlavors))."\n",
            FILE_APPEND
        );

        // DEBUG: Log query param detection
        file_put_contents(storage_path('logs/theme-none-render.log'),
            "Query param check:\n".
            '  queryTheme: '.var_export($queryTheme, true)."\n".
            '  queryFlavor: '.var_export($queryFlavor, true)."\n".
            '  queryAccent: '.var_export($queryAccent, true)."\n".
            '  condition met: '.var_export(($queryTheme !== null && $queryFlavor !== null && $queryAccent !== null), true)."\n",
            FILE_APPEND
        );

        // CRITICAL: If query parameters are present, ALWAYS use them (override everything)
        // This ensures query parameters work even if mount() used defaults
        if ($queryTheme !== null && $queryFlavor !== null && $queryAccent !== null) {
            // Validate query parameters
            $themeEnum = Theme::tryFrom($queryTheme);
            $flavorEnum = ThemeFlavor::tryFrom($queryFlavor);
            $accentEnum = ThemeAccent::tryFrom($queryAccent);

            file_put_contents(storage_path('logs/theme-none-render.log'),
                '  Enums valid: '.var_export(($themeEnum && $flavorEnum && $accentEnum), true)."\n".
                '  themeEnum: '.var_export($themeEnum?->value, true)."\n",
                FILE_APPEND
            );

            if ($themeEnum && $flavorEnum && $accentEnum) {
                // Clear session when query params are present
                Session::forget(['preview_theme', 'preview_flavor', 'preview_accent']);

                // SEQUENCE 1: Set theme FIRST
                $this->theme = $queryTheme;

                file_put_contents(storage_path('logs/theme-none-render.log'),
                    "  SET theme to: {$this->theme}\n",
                    FILE_APPEND
                );

                // SEQUENCE 2: Immediately refresh available options (this uses $this->theme)
                $this->refreshAvailableOptions();

                // SEQUENCE 3: Now set flavor and accent (after availableFlavors/Accents are updated)
                $flavorValues = array_map(fn ($f) => $f->value, $this->availableFlavors);
                if (in_array($queryFlavor, $flavorValues, true)) {
                    $this->flavor = $queryFlavor;
                } else {
                    $this->flavor = $flavorValues[0] ?? 'default';
                }

                $accentValues = array_map(fn ($a) => $a->value, $this->availableAccents);
                if (in_array($queryAccent, $accentValues, true)) {
                    $this->accent = $queryAccent;
                } else {
                    $this->accent = $accentValues[0] ?? 'primary';
                }

                // SEQUENCE 4: Update session last
                Session::put([
                    'preview_theme' => $this->theme,
                    'preview_flavor' => $this->flavor,
                    'preview_accent' => $this->accent,
                ]);
            }
        } elseif (empty($this->theme) || $this->theme === '') {
            // If no query params and component properties are uninitialized, use defaults
            $defaultThemeData = $this->themeService->resolveThemeData(null);
            $this->theme = $defaultThemeData->theme->value;
            $this->flavor = $defaultThemeData->theme === Theme::None ? 'default' : $defaultThemeData->flavor->value;
            $this->accent = $defaultThemeData->theme === Theme::None ? 'primary' : $defaultThemeData->accent->value;
            $this->refreshAvailableOptions();
        }

        // FINAL CHECK: Ensure availableFlavors matches current theme before rendering
        // This is a safety check in case something went wrong earlier
        if (! empty($this->theme) && $this->theme !== 'none') {
            $finalThemeEnum = Theme::tryFrom($this->theme);
            if ($finalThemeEnum) {
                $expectedFlavors = $finalThemeEnum->flavors();
                $expectedFlavorValues = array_map(fn ($f) => $f->value, $expectedFlavors);
                $currentFlavorValues = array_map(fn ($f) => $f->value, $this->availableFlavors);

                // If flavors don't match, force refresh one more time
                if ($expectedFlavorValues !== $currentFlavorValues) {
                    $this->refreshAvailableOptions();
                }
            }
        }

        // Build theme data structure for JavaScript (all themes with their flavors and accents)
        $themesData = [];
        foreach (Theme::cases() as $themeEnum) {
            $themesData[$themeEnum->value] = [
                'flavors' => array_map(fn ($f) => $f->value, $themeEnum->flavors()),
                'accents' => array_map(fn ($a) => $a->value, $this->accentMapper->getAvailableAccents($themeEnum)),
            ];
        }

        // Return the view with theme data and layout
        // Explicitly pass component properties to ensure they're available in the Blade template

        // DEBUG: ALWAYS log what we're about to pass to view
        file_put_contents(storage_path('logs/render-before-view.log'),
            "=== RIGHT BEFORE VIEW ===\n".
            "this->theme: '{$this->theme}'\n".
            "this->flavor: '{$this->flavor}'\n".
            "request()->query('theme'): '".(request()->query('theme') ?? 'null')."'\n\n",
            FILE_APPEND
        );

        $viewData = [
            'themesData' => $themesData,
            'theme' => $this->theme,
            'flavor' => $this->flavor,
            'accent' => $this->accent,
            'availableFlavors' => $this->availableFlavors,
            'availableAccents' => $this->availableAccents,
        ];

        // DEBUG: Log final component state for 'none' theme
        if ($this->theme === 'none') {
            file_put_contents(storage_path('logs/theme-none-render.log'),
                "FINAL COMPONENT STATE (render method end):\n".
                "  theme: {$this->theme}\n".
                "  flavor: {$this->flavor}\n".
                "  accent: {$this->accent}\n".
                '  availableFlavors count: '.count($this->availableFlavors)."\n".
                '  availableAccents count: '.count($this->availableAccents)."\n\n",
                FILE_APPEND
            );
        }

        // DEBUG: Log what we're passing to the view
        $debugRender = [
            'query_theme' => $request->query('theme'),
            'query_flavor' => $request->query('flavor'),
            'query_accent' => $request->query('accent'),
            'component_theme' => $this->theme,
            'component_flavor' => $this->flavor,
            'component_accent' => $this->accent,
            'view_theme' => $viewData['theme'],
            'view_flavor' => $viewData['flavor'],
            'view_accent' => $viewData['accent'],
            'available_flavors_count' => count($viewData['availableFlavors']),
            'available_flavors' => array_map(fn ($f) => $f->value, $viewData['availableFlavors']),
        ];
        file_put_contents(storage_path('logs/theme-preview-render.log'), json_encode($debugRender, JSON_PRETTY_PRINT)."\n\n", FILE_APPEND);

        return view('livewire.themes.preview', $viewData)->layout('layouts.app');
    }

    private function refreshAvailableOptions(): void
    {
        // DEBUG: Log what theme we're using
        file_put_contents(storage_path('logs/theme-preview-sequence.log'),
            'refreshAvailableOptions called with theme: '.($this->theme ?? 'null')."\n",
            FILE_APPEND
        );

        // Handle None theme or invalid theme
        if ($this->theme === 'none' || empty($this->theme)) {
            $this->availableFlavors = [];
            $this->availableAccents = [];
            $this->flavor = 'default';
            $this->accent = 'primary';

            return;
        }

        // Safely get theme enum
        $themeEnum = Theme::tryFrom($this->theme);
        if (! $themeEnum) {
            // Fallback to default theme if invalid
            file_put_contents(storage_path('logs/theme-preview-sequence.log'),
                "  WARNING: Theme '{$this->theme}' not found, falling back to Catppuccin\n",
                FILE_APPEND
            );
            $themeEnum = Theme::Catppuccin;
            $this->theme = $themeEnum->value;
        }

        $this->availableFlavors = $themeEnum->flavors();
        $this->availableAccents = $this->accentMapper->getAvailableAccents($themeEnum);

        // DEBUG: Log what we set
        file_put_contents(storage_path('logs/theme-preview-sequence.log'),
            "  Theme enum: {$themeEnum->value}\n".
            '  Flavors set: '.json_encode(array_map(fn ($f) => $f->value, $this->availableFlavors))."\n",
            FILE_APPEND
        );

        // DEBUG: Log what we set
        if (app()->environment('testing')) {
            \Log::debug('ThemePreview refreshAvailableOptions()', [
                'theme' => $this->theme,
                'theme_enum' => $themeEnum->value,
                'available_flavors_count' => count($this->availableFlavors),
                'available_flavors' => array_map(fn ($f) => $f->value, $this->availableFlavors),
                'available_accents_count' => count($this->availableAccents),
                'available_accents' => array_map(fn ($a) => $a->value, $this->availableAccents),
            ]);
        }

        // Handle None theme - no flavors or accents
        if ($themeEnum === Theme::None) {
            $this->flavor = 'default';
            $this->accent = 'primary';

            return;
        }

        // If current flavor is not available, use first available
        $flavorValues = array_map(fn ($f) => $f->value, $this->availableFlavors);
        if (! in_array($this->flavor, $flavorValues, true)) {
            $this->flavor = $flavorValues[0] ?? 'default';
        }

        // If current accent is not available, use first available
        $accentValues = array_map(fn ($a) => $a->value, $this->availableAccents);
        if (! in_array($this->accent, $accentValues, true)) {
            $this->accent = $accentValues[0] ?? 'primary';
        }
    }

    private function updateSessionAndDom(): void
    {
        // Update session
        Session::put([
            'preview_theme' => $this->theme,
            'preview_flavor' => $this->flavor,
            'preview_accent' => $this->accent,
        ]);

        // Record preview_interaction event (T027a, FR-036, T027g, FR-103)
        $this->recordPreviewInteraction();

        // Calculate isLight state
        $themeEnum = Theme::from($this->theme);
        $flavorEnum = $this->theme === 'none' ? ThemeFlavor::Default : ThemeFlavor::from($this->flavor);
        $accentEnum = $this->theme === 'none' ? ThemeAccent::Primary : ThemeAccent::from($this->accent);
        $themeData = new ThemeData(
            theme: $themeEnum,
            flavor: $flavorEnum,
            accent: $accentEnum,
        );
        $isLight = $themeData->isLight();

        // Update DOM immediately using $this->js() for SPA behavior (no page reload)
        // For None theme, pass null to remove data attributes
        $this->js(
            sprintf(
                'if (typeof window !== "undefined" && window.__liveThemePreview) { window.__liveThemePreview(%s); }',
                Js::from([
                    'theme' => $this->theme === 'none' ? null : $this->theme,
                    'flavor' => $this->theme === 'none' ? null : $this->flavor,
                    'accent' => $this->theme === 'none' ? null : $this->accent,
                    'isLight' => $isLight,
                ])
            )
        );

        // Also dispatch event for consistency with other theme updates
        $this->dispatch(
            'theme-updated',
            theme: $this->theme === 'none' ? null : $this->theme,
            flavor: $this->theme === 'none' ? null : $this->flavor,
            accent: $this->theme === 'none' ? null : $this->accent,
            isLight: $isLight,
        );
    }

    private function recordPreviewInteraction(): void
    {
        // Measure performance if available (T027g, FR-103)
        $performanceData = [];
        if (function_exists('microtime')) {
            $performanceData['dom_update_time'] = microtime(true) * 1000; // milliseconds
        }

        // Log preview interaction (T027a, FR-036, T027g, FR-103)
        Log::info('Preview page interaction', [
            'event_type' => 'preview_interaction',
            'theme' => $this->theme,
            'flavor' => $this->flavor,
            'accent' => $this->accent,
            'timestamp' => now()->toIso8601String(),
            'performance' => $performanceData,
            'page_url' => request()->fullUrl(),
            'referrer' => request()->header('Referer'),
        ]);
    }

    private function safeThemeFromValue(string $value): Theme
    {
        try {
            return Theme::from($value);
        } catch (ValueError $e) {
            return Theme::Catppuccin;
        }
    }

    private function valueWithinFlavors(string $value): string
    {
        // None theme has no flavors
        if ($this->theme === 'none') {
            return 'default';
        }

        $flavorValues = array_map(fn ($f) => $f->value, $this->availableFlavors);
        if (in_array($value, $flavorValues, true)) {
            return $value;
        }

        return $flavorValues[0] ?? 'default';
    }

    private function valueWithinAccents(string $value): string
    {
        // None theme has no accents
        if ($this->theme === 'none') {
            return 'primary';
        }

        $accentValues = array_map(fn ($a) => $a->value, $this->availableAccents);
        if (in_array($value, $accentValues, true)) {
            return $value;
        }

        return $accentValues[0] ?? 'primary';
    }
};
?>

@php
    // CRITICAL WORKAROUND: Override component state from query parameters for GET requests
    // Issue: mount() is not being called or not processing query params correctly for HTTP GET
    // This ensures theme, flavor, and accent from URL query params are always respected
    if (request()->isMethod('GET') && request()->has('theme')) {
        $queryTheme = request()->query('theme');
        $queryFlavor = request()->query('flavor');
        $queryAccent = request()->query('accent');
        
        if ($queryTheme === 'none') {
            // Special case: 'none' theme has no flavors or accents
            $theme = 'none';
            $flavor = 'default';
            $accent = 'primary';
            $availableFlavors = [];
            $availableAccents = [];
        } elseif ($queryTheme && $queryFlavor && $queryAccent) {
            // Override component state with query parameters
            $themeEnum = \App\Enums\Theme::tryFrom($queryTheme);
            if ($themeEnum) {
                $theme = $queryTheme;
                $flavor = $queryFlavor;
                $accent = $queryAccent;
                // Update available options based on the query parameter theme
                $availableFlavors = $themeEnum->flavors();
                $accentMapper = app(\App\Contracts\ThemeAccentMapperInterface::class);
                $availableAccents = $accentMapper->getAvailableAccents($themeEnum);
            }
        }
    }
@endphp

<div class="container mx-auto px-4 py-8 max-w-4xl">
        <!-- Preview Mode Banner (T020a, FR-080) -->
        <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-sm text-blue-800 dark:text-blue-200">
                    <strong>Preview Mode</strong> - Changes are temporary and will reset when you leave this page.
                </p>
            </div>
        </div>

        <div class="space-y-8">
            <div>
                <h1 class="text-3xl font-bold mb-2">Theme Preview</h1>
                <p class="text-gray-600 dark:text-gray-400">Try out different themes and see how they look.</p>
            </div>

            <!-- Theme Selection -->
            <div>
                <h2 class="text-xl font-semibold mb-2">Theme Family</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Choose your preferred aesthetic.</p>

                <select
                    wire:model.live="theme"
                    class="w-full sm:w-auto min-w-[200px] px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent theme-transition"
                    aria-label="Theme family selection"
                >
                    @foreach(Theme::cases() as $themeCase)
                        <option value="{{ $themeCase->value }}">{{ $themeCase->label() }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Flavor Selection (T020b, FR-007, FR-011) -->
            @php
                // DEBUG: Dump for 'none' theme troubleshooting
                if (app()->environment('testing') && request()->query('theme') === 'none') {
                    file_put_contents(storage_path('logs/blade-template-debug.log'),
                        "BLADE TEMPLATE - Flavor Section:\n" .
                        "  \$theme: " . var_export($theme, true) . "\n" .
                        "  count(\$availableFlavors): " . count($availableFlavors) . "\n" .
                        "  condition result: " . var_export(($theme !== 'none' && count($availableFlavors) > 1), true) . "\n\n",
                        FILE_APPEND
                    );
                }
            @endphp
            @if($theme !== 'none' && count($availableFlavors) > 1)
                <div class="transition-all duration-300 ease-in-out">
                    <h2 class="text-xl font-semibold mb-2">Variant</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Select a flavor for the chosen theme.</p>

                    <div class="grid gap-4 sm:grid-cols-4" role="radiogroup" aria-label="Theme variant selection">
                        @foreach($availableFlavors as $flavorEnum)
                            <label class="flex items-center gap-3 p-4 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 theme-transition
                                {{ $flavor === $flavorEnum->value ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-700' }}">
                                <input
                                    type="radio"
                                    wire:model.live="flavor"
                                    value="{{ $flavorEnum->value }}"
                                />
                                <span>{{ $flavorEnum->label() }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Accent Selection (T020b, FR-007, FR-011) -->
            @if($theme !== 'none' && count($availableAccents) > 0)
<div>
                    <h2 class="text-xl font-semibold mb-2">Accent Colors</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Choose accent colors for different UI elements.</p>

                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4" role="radiogroup" aria-label="Accent color selection">
                        @foreach($availableAccents as $accentEnum)
                            @php
                                $isSelected = $accent === $accentEnum->value;

                                // Define preview element and description for each accent
                                // Use direct CSS variable names so each card shows its own accent color
                                $previewConfig = match($accentEnum) {
                                    ThemeAccent::Primary => [
                                        'element' => 'button',
                                        'label' => 'Theme',
                                        'description' => 'Brand/Default',
                                        'cssVar' => '--accent-primary',
                                    ],
                                    ThemeAccent::Blue => [
                                        'element' => 'badge',
                                        'label' => 'Blue',
                                        'description' => 'Information',
                                        'cssVar' => '--accent-blue',
                                    ],
                                    ThemeAccent::Red => [
                                        'element' => 'error',
                                        'label' => 'Red',
                                        'description' => 'Destructive/Error',
                                        'cssVar' => '--accent-red',
                                    ],
                                    ThemeAccent::Green => [
                                        'element' => 'success',
                                        'label' => 'Green',
                                        'description' => 'Success',
                                        'cssVar' => '--accent-green',
                                    ],
                                };
                            @endphp
                            <label
                                class="flex flex-col gap-3 p-4 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 theme-transition transition-transform hover:scale-105 active:scale-95
                                    {{ $isSelected ? 'accent-selected ring-2 ring-opacity-50' : 'border-gray-300 dark:border-gray-700' }}"
                                data-accent-option="{{ $accentEnum->value }}"
                                wire:key="accent-{{ $accentEnum->value }}-{{ $accent }}"
                                style="{{ $isSelected ? 'border-color: var(' . $previewConfig['cssVar'] . '); background-color: color-mix(in srgb, var(' . $previewConfig['cssVar'] . ') 20%, transparent); ring-color: var(' . $previewConfig['cssVar'] . ');' : '' }}"
                                <input
                                    type="radio"
                                    wire:model.live="accent"
                                    value="{{ $accentEnum->value }}"
                                    class="sr-only"
                                />
                                <div class="flex justify-center" data-accent-preview="{{ $accentEnum->value }}">
                                    @if($previewConfig['element'] === 'button')
                                        <button class="px-4 py-2 rounded-lg text-white font-medium text-sm" style="background-color: var({{ $previewConfig['cssVar'] }});">Button</button>
                                    @elseif($previewConfig['element'] === 'badge')
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium text-white" style="background-color: var({{ $previewConfig['cssVar'] }});">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                            </svg>
                                            Info
                                        </span>
                                    @elseif($previewConfig['element'] === 'error')
                                        <div class="flex items-center gap-2 text-sm">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" style="color: var({{ $previewConfig['cssVar'] }});">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                            </svg>
                                            <span style="color: var({{ $previewConfig['cssVar'] }});">Error</span>
                                        </div>
                                    @elseif($previewConfig['element'] === 'success')
                                        <div class="flex items-center gap-2 text-sm">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" style="color: var({{ $previewConfig['cssVar'] }});">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            <span style="color: var({{ $previewConfig['cssVar'] }});">Success</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="text-center">
                                    <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $previewConfig['label'] }}</div>
                                    <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $previewConfig['description'] }}</div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        // Server-provided theme data (all themes with their flavors and accents)
        window.themeData = @json($themesData);
    </script>
    @endpush
</div>
