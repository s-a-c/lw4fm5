<?php

use Livewire\Component;
use App\Data\UserSettingsData;
use App\Enums\Theme;
use App\Enums\ThemeFlavor;
use App\Enums\ThemeAccent;

new class extends Component {

    public string $theme;
    public string $flavor;
    public string $accent;

    // Dynamic available flavors based on Theme
    public array $availableFlavors = [];

    public function mount(): void
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        // Fallback to defaults if settings are null
        $settings = $user->settings ?? new UserSettingsData();

        // Populate local state from the DTO
        $this->theme = $settings->theme->value;
        $this->flavor = $settings->flavor->value;
        $this->accent = $settings->accent->value;

        $this->updateAvailableFlavors();
    }

    public function updateAvailableFlavors(): void
    {
        // Get the specific flavors allowed for the current theme
        $themeEnum = Theme::from($this->theme);
        $this->availableFlavors = $themeEnum->flavors();

        // Safety: If current flavor doesn't belong to new theme, reset to first available
        $currentFlavorEnum = ThemeFlavor::tryFrom($this->flavor);
        if (!in_array($currentFlavorEnum, $this->availableFlavors)) {
            $this->flavor = $this->availableFlavors[0]->value;
            // Trigger save immediately so state is consistent
            $this->updated('flavor', $this->flavor);
        }
    }

    public function updated(string $property, mixed $value): void
    {
        if ($property === 'theme') {
            $this->updateAvailableFlavors();
        }

        /** @var \App\Models\User $user */
        $user = auth()->user();
        $settings = $user->settings ?? new UserSettingsData();

        // Update DTO properties
        if ($property === 'theme') $settings->theme = Theme::from($value);
        if ($property === 'flavor') $settings->flavor = ThemeFlavor::from($value);
        if ($property === 'accent') $settings->accent = ThemeAccent::from($value);

        $user->settings = $settings;
        $user->save();

        // JS: Instant DOM Update without reload
        $this->js(<<<'JS'
            const r = document.documentElement;
            r.dataset.theme = $wire.theme;
            r.dataset.flavor = $wire.flavor;
            r.dataset.accent = $wire.accent;

            // Logic to determine if we should remove 'dark' class
            // This relies on your flavor enum conventions (e.g., latte, lotus)
            const lightFlavors = ['latte', 'lotus', 'day', 'ayu-light'];
            if (lightFlavors.includes($wire.flavor)) {
                r.classList.remove('dark');
            } else {
                r.classList.add('dark');
            }
        JS);

        // Optional: Dispatch a Flux Toast
        $this->dispatch('flux-toast', text: 'Theme updated!', variant: 'success');
    }
};
?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Appearance')" :subheading="__('Customize the look and feel of your workspace')">

        <div class="space-y-8">

            <!-- 1. THEME SELECTION -->
            <flux:fieldset>
                <flux:legend>Theme Family</flux:legend>
                <flux:subheading>Choose your preferred aesthetic.</flux:subheading>

                <div class="grid grid-cols-2 gap-4 mt-4">
                    @foreach(Theme::cases() as $case)
                        <flux:radio
                            wire:model.live="theme"
                            :value="$case->value"
                            :label="$case->label()"
                            variant="cards"
                        />
                    @endforeach
                </div>
            </flux:fieldset>

            <flux:separator />

            <!-- 2. FLAVOR SELECTION (Dynamic) -->
            <flux:fieldset>
                <flux:legend>Variant</flux:legend>
                <flux:subheading>Select a flavor for the chosen theme.</flux:subheading>

                <flux:radio.group wire:model.live="flavor" class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4">
                    @foreach($availableFlavors as $flavorEnum)
                        <flux:radio
                            :value="$flavorEnum->value"
                            :label="$flavorEnum->label()"
                        >
                            <x-slot:icon>
                                <!-- A simple circle to indicate this is a color choice -->
                                <div class="size-4 rounded-full border border-gray-500/50 bg-zinc-800"></div>
                            </x-slot>
                        </flux:radio>
                    @endforeach
                </flux:radio.group>
            </flux:fieldset>

            <flux:separator />

            <!-- 3. ACCENT SELECTION -->
            <flux:fieldset>
                <flux:legend>Accent Color</flux:legend>
                <flux:subheading>Pick a primary color for buttons and links.</flux:subheading>

                <flux:radio.group wire:model.live="accent" class="flex flex-wrap gap-4 mt-4">
                    @foreach(ThemeAccent::cases() as $accentEnum)
                        <flux:radio :value="$accentEnum->value" :label="$accentEnum->label()" />
                    @endforeach
                </flux:radio.group>
            </flux:fieldset>

            <!-- 4. VISUAL PREVIEW -->
            <div class="mt-8">
                <flux:heading size="lg" class="mb-4">Preview</flux:heading>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Logo Animation -->
                    <div class="p-6 rounded-xl bg-zinc-900 border border-zinc-700 flex flex-col items-center justify-center h-48 transition-colors duration-300">
                        <!-- Uses 'is-animated' or 'is-animated-kg' depending on theme via simple logic -->
                        <svg viewBox="0 0 512 512"
                            class="w-24 h-24 {{ $theme === 'kanagawa' ? 'is-animated-kg' : 'is-animated' }}">
                            <circle cx="256" cy="256" r="256" fill="currentColor" class="opacity-10" />
                            <!-- Add your actual logo path here -->
                            <circle cx="256" cy="256" r="128" fill="currentColor" />
                        </svg>
                        <span class="mt-4 text-sm font-medium text-zinc-400">Logo Animation</span>
                    </div>

                    <!-- UI Elements -->
                    <div class="space-y-4 p-6 rounded-xl bg-zinc-900 border border-zinc-700 transition-colors duration-300">
                        <flux:button variant="primary" class="w-full">Primary Action</flux:button>
                        <flux:button class="w-full">Secondary Action</flux:button>
                        <flux:input placeholder="Input field..." />
                    </div>
                </div>
            </div>

        </div>
    </x-settings.layout>
</section>
