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
    public string $theme;

    public string $flavor;

    public string $accent;

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
        // Get theme data from query parameters or session, or use defaults
        $sessionTheme = $theme ?? Session::get('preview_theme');
        $sessionFlavor = $flavor ?? Session::get('preview_flavor');
        $sessionAccent = $accent ?? Session::get('preview_accent');

        // Resolve theme data from session or use defaults
        $themeData = null;
        if ($sessionTheme && $sessionFlavor && $sessionAccent) {
            $themeEnum = Theme::tryFrom($sessionTheme);
            $flavorEnum = ThemeFlavor::tryFrom($sessionFlavor);
            $accentEnum = ThemeAccent::tryFrom($sessionAccent);

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

        // Use defaults if session is empty or invalid
        if (!$themeData) {
            $themeData = $this->themeService->resolveThemeData(null);
        }

        $this->theme = $themeData->theme->value;
        $this->flavor = $themeData->flavor->value;
        $this->accent = $themeData->accent->value;

        // Save to session
        Session::put([
            'preview_theme' => $this->theme,
            'preview_flavor' => $this->flavor,
            'preview_accent' => $this->accent,
        ]);

        $this->refreshAvailableOptions();
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

    public function render(): \Illuminate\Contracts\View\View
    {
        // Build theme data structure for JavaScript (all themes with their flavors and accents)
        $themesData = [];
        foreach (Theme::cases() as $themeEnum) {
            $themesData[$themeEnum->value] = [
                'flavors' => array_map(fn($f) => $f->value, $themeEnum->flavors()),
                'accents' => array_map(fn($a) => $a->value, $this->accentMapper->getAvailableAccents($themeEnum)),
            ];
        }

        return view('livewire.themes.preview', [
            'themesData' => $themesData,
        ]);
    }

    private function refreshAvailableOptions(): void
    {
        $themeEnum = Theme::from($this->theme);
        $this->availableFlavors = $themeEnum->flavors();
        $this->availableAccents = $this->accentMapper->getAvailableAccents($themeEnum);

        // If current flavor is not available, use first available
        $flavorValues = array_map(fn($f) => $f->value, $this->availableFlavors);
        if (!in_array($this->flavor, $flavorValues, true)) {
            $this->flavor = $flavorValues[0] ?? 'mocha';
        }

        // If current accent is not available, use first available
        $accentValues = array_map(fn($a) => $a->value, $this->availableAccents);
        if (!in_array($this->accent, $accentValues, true)) {
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

        // Update DOM immediately using $this->js() for SPA behavior (no page reload)
        $this->js(
            sprintf(
                'if (typeof window !== "undefined" && window.__liveThemePreview) { window.__liveThemePreview(%s); }',
                Js::from([
                    'theme' => $this->theme,
                    'flavor' => $this->flavor,
                    'accent' => $this->accent,
                ])
            )
        );

        // Also dispatch event for consistency with other theme updates
        $this->dispatch(
            'theme-updated',
            theme: $this->theme,
            flavor: $this->flavor,
            accent: $this->accent,
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
        } catch (\ValueError $e) {
            return Theme::Catppuccin;
        }
    }

    private function valueWithinFlavors(string $value): string
    {
        $flavorValues = array_map(fn($f) => $f->value, $this->availableFlavors);
        if (in_array($value, $flavorValues, true)) {
            return $value;
        }

        return $flavorValues[0] ?? 'mocha';
    }

    private function valueWithinAccents(string $value): string
    {
        $accentValues = array_map(fn($a) => $a->value, $this->availableAccents);
        if (in_array($value, $accentValues, true)) {
            return $value;
        }

        return $accentValues[0] ?? 'primary';
    }
};
?>

<x-layouts.app>
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

                <div class="grid gap-4 sm:grid-cols-2" role="radiogroup" aria-label="Theme family selection">
                    @foreach(Theme::cases() as $themeCase)
                        <label class="flex items-center gap-3 p-4 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 theme-transition
                            {{ $theme === $themeCase->value ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-700' }}">
                            <input
                                type="radio"
                                wire:model.live="theme"
                                value="{{ $themeCase->value }}"
                            />
                            <span>{{ $themeCase->label() }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Flavor Selection (T020b, FR-007, FR-011) -->
            @if(count($availableFlavors) > 1)
                <div>
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
            <div>
                <h2 class="text-xl font-semibold mb-2">Accent Color</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Pick a primary color for buttons and links.</p>

                <div class="flex flex-wrap gap-3" role="radiogroup" aria-label="Accent color selection">
                    @foreach($availableAccents as $accentEnum)
                        <label class="flex items-center gap-3 p-4 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 theme-transition
                            {{ $accent === $accentEnum->value ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-700' }}">
                            <input
                                type="radio"
                                wire:model.live="accent"
                                value="{{ $accentEnum->value }}"
                            />
                            <div class="size-4 rounded-full border border-white/20"
                                 style="background-color: var(--accent-{{ $accentEnum->value }}, var(--color-accent));"
                                 aria-hidden="true"></div>
                            <span>{{ $accentEnum->label() }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Server-provided theme data (all themes with their flavors and accents)
        window.themeData = @json($themesData);
    </script>
    @endpush
</x-layouts.app>
