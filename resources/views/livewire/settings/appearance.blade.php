@php
    use App\Enums\Theme;
@endphp

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Appearance')" :subheading="__('Customize the look and feel of your workspace')">

        <!-- ARIA Live Region for Theme Change Announcements (T013e, FR-023) -->
        <div
            id="theme-announcements"
            aria-live="polite"
            aria-atomic="true"
            class="sr-only"
            role="status"
        ></div>

        <div class="space-y-8">

            <!-- 1. THEME SELECTION -->
            <flux:fieldset>
                <flux:legend>Theme Family</flux:legend>
                <flux:subheading>Choose your preferred aesthetic.</flux:subheading>

                <flux:radio.group
                    wire:model.live="theme"
                    class="grid gap-4 mt-4 sm:grid-cols-2"
                    aria-label="{{ __('Theme family selection') }}"
                >
                    @foreach(Theme::cases() as $case)
                        <flux:radio
                            class="theme-transition"
                            data-theme-choice="theme"
                            wire:key="theme-{{ $case->value }}"
                            :value="$case->value"
                            :label="$case->label()"
                            :data-test="'appearance-theme-'.$case->value"
                            :aria-label="__('Select :theme theme', ['theme' => $case->label()])"
                        />
                    @endforeach
                </flux:radio.group>
            </flux:fieldset>

            <flux:separator />

            <!-- 2. FLAVOR SELECTION (Dynamic) -->
            <flux:fieldset>
                <flux:legend>Variant</flux:legend>
                <flux:subheading>Select a flavor for the chosen theme.</flux:subheading>

                @if(count($availableFlavors) > 1)
                    <flux:radio.group
                        wire:model.live="flavor"
                        class="theme-transition grid gap-4 mt-4 sm:grid-cols-4"
                        aria-label="{{ __('Theme variant selection') }}"
                    >
                        @foreach($availableFlavors as $flavorEnum)
                            <flux:radio
                                class="theme-transition"
                                data-theme-choice="flavor"
                                wire:key="flavor-{{ $flavorEnum->value }}"
                                :value="$flavorEnum->value"
                                :label="$flavorEnum->label()"
                                :aria-label="__('Select :flavor variant', ['flavor' => $flavorEnum->label()])"
                            >
                                <x-slot:icon>
                                    <div class="size-4 rounded-full border border-gray-500/50 bg-zinc-800" aria-hidden="true"></div>
                                </x-slot>
                            </flux:radio>
                        @endforeach
                    </flux:radio.group>
                @else
                    <p class="theme-transition text-sm text-zinc-400 mt-4">
                        {{ __('This theme has a single curated variant.') }}
                    </p>
                @endif
            </flux:fieldset>

            <flux:separator />

            <!-- 3. ACCENT SELECTION -->
            <flux:fieldset>
                <flux:legend>Accent Color</flux:legend>
                <flux:subheading>Pick a primary color for buttons and links.</flux:subheading>

                <flux:radio.group
                    wire:model.live="accent"
                    class="theme-transition flex flex-wrap gap-3 mt-4 sm:flex-row max-sm:flex-col"
                    aria-label="{{ __('Accent color selection') }}"
                >
                    @foreach($availableAccents as $accentEnum)
                        <flux:radio
                            class="theme-transition"
                            data-theme-choice="accent"
                            wire:key="accent-{{ $accentEnum->value }}"
                            :value="$accentEnum->value"
                            :label="$accentEnum->label()"
                            :data-test="'appearance-accent-'.$accentEnum->value"
                            :aria-label="__('Select :accent accent color', ['accent' => $accentEnum->label()])"
                        >
                            <x-slot:icon>
                                <div
                                    class="size-4 rounded-full border border-white/20"
                                    style="background-color: var(--accent-{{ $accentEnum->value }}, var(--color-accent));"
                                    aria-hidden="true"
                                ></div>
                            </x-slot>
                        </flux:radio>
                    @endforeach
                </flux:radio.group>
            </flux:fieldset>

            @if($showReset)
                <div class="flex justify-end">
                    <flux:button
                        wire:click="resetToDefault"
                        wire:loading.attr="disabled"
                        icon="arrow-path"
                        variant="subtle"
                        data-test="reset-theme-button"
                    >
                        Reset to Default
                    </flux:button>
                </div>
            @endif

            <!-- 4. VISUAL PREVIEW -->
            <div class="mt-8">
                <flux:heading size="lg" class="mb-4">Preview</flux:heading>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Logo Animation -->
                    <div class="theme-transition p-6 rounded-xl bg-zinc-900 border border-zinc-700 flex flex-col items-center justify-center h-48">
                        <svg viewBox="0 0 512 512"
                            class="w-24 h-24 {{ $theme === 'kanagawa' ? 'is-animated-kg' : 'is-animated' }}">
                            <circle cx="256" cy="256" r="256" fill="currentColor" class="opacity-10" />
                            <circle cx="256" cy="256" r="128" fill="currentColor" />
                        </svg>
                        <span class="mt-4 text-sm font-medium text-zinc-300 dark:text-zinc-500">Logo Animation</span>
                    </div>

                    <!-- UI Elements -->
                    <div class="theme-transition space-y-4 p-6 rounded-xl bg-zinc-900 border border-zinc-700">
                        <flux:button variant="primary" class="w-full">Primary Action</flux:button>
                        <flux:button class="w-full">Secondary Action</flux:button>
                        <flux:input placeholder="Input field..." />
                    </div>
                </div>
            </div>

        </div>
    </x-settings.layout>
</section>
