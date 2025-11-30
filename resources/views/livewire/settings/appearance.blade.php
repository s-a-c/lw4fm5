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

        <div class="space-y-10">

            <!-- 1. THEME SELECTION -->
            <div class="space-y-3">
                <div>
                    <flux:heading size="base" class="mb-1">{{ __('Theme Family') }}</flux:heading>
                    <flux:subheading class="text-sm">{{ __('Choose your preferred aesthetic') }}</flux:subheading>
                </div>

                <flux:select
                    wire:model.live="theme"
                    variant="listbox"
                    placeholder="{{ __('Select a theme...') }}"
                    :data-test="'appearance-theme-select'"
                    class="w-full"
                >
                    @foreach(Theme::cases() as $case)
                        <flux:select.option
                            wire:key="theme-{{ $case->value }}"
                            :value="$case->value"
                            :data-test="'appearance-theme-'.$case->value"
                        >
                            {{ $case->label() }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </div>

            {{-- <flux:separator /> --}}

            <!-- 2. FLAVOR SELECTION (Dynamic) -->
            @if(count($availableFlavors) > 0)
                <div class="space-y-3">
                    <div>
                        <flux:heading size="base" class="mb-1">{{ __('Variant') }}</flux:heading>
                        <flux:subheading class="text-sm">{{ __('Select a flavor for the chosen theme') }}</flux:subheading>
                    </div>

                    @if(count($availableFlavors) > 1)
                        <flux:select
                            wire:model.live="flavor"
                            variant="listbox"
                            placeholder="{{ __('Select a variant...') }}"
                            :data-test="'appearance-flavor-select'"
                            class="w-full"
                        >
                            @foreach($availableFlavors as $flavorEnum)
                                <flux:select.option
                                    wire:key="flavor-{{ $flavorEnum->value }}"
                                    :value="$flavorEnum->value"
                                    :data-test="'appearance-flavor-'.$flavorEnum->value"
                                >
                                    {{ $flavorEnum->label() }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    @else
                        <div class="theme-transition rounded-lg border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50 p-4">
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                {{ __('This theme has a single curated variant.') }}
                            </p>
                        </div>
                    @endif
                </div>

                {{-- <flux:separator /> --}}
            @endif

            <!-- 4. VISUAL PREVIEW -->
            <div class="pt-6 border-t border-zinc-200 dark:border-zinc-800">
                <div class="mb-4">
                    <flux:heading size="base" class="mb-1">{{ __('Preview') }}</flux:heading>
                    <flux:subheading class="text-sm">{{ __('See how your theme looks') }}</flux:subheading>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <!-- Logo Animation -->
                    <div class="theme-transition p-6 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50 flex flex-col items-center justify-center min-h-[200px]">
                        @if($theme === 'kanagawa')
                            <svg viewBox="0 0 512 512" class="w-20 h-20" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="256" cy="256" r="256" fill="currentColor" class="opacity-10" />
                                <circle id="kg-part-1" class="is-animated-kg" cx="256" cy="128" r="40" fill="var(--kg-purple, #957fb8)" />
                                <circle id="kg-part-2" class="is-animated-kg" cx="384" cy="192" r="40" fill="var(--kg-blue, #7e9cd8)" />
                                <circle id="kg-part-3" class="is-animated-kg" cx="384" cy="320" r="40" fill="var(--kg-green, #76946a)" />
                                <circle id="kg-part-4" class="is-animated-kg" cx="256" cy="384" r="40" fill="var(--kg-yellow, #c0a36e)" />
                                <circle id="kg-part-5" class="is-animated-kg" cx="128" cy="320" r="40" fill="var(--kg-orange, #ffa066)" />
                                <circle id="kg-part-6" class="is-animated-kg" cx="128" cy="192" r="40" fill="var(--kg-red, #c34043)" />
                            </svg>
                        @else
                            <svg viewBox="0 0 512 512" class="w-20 h-20" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="256" cy="256" r="256" fill="currentColor" class="opacity-10" />
                                <circle id="mauve" class="is-animated" cx="256" cy="128" r="40" fill="var(--logo-mauve, #cba6f7)" />
                                <circle id="sapphire" class="is-animated" cx="384" cy="192" r="40" fill="var(--logo-sapphire, #74c7ec)" />
                                <circle id="green" class="is-animated" cx="384" cy="320" r="40" fill="var(--logo-green, #a6e3a1)" />
                                <circle id="yellow" class="is-animated" cx="256" cy="384" r="40" fill="var(--logo-yellow, #f9e2af)" />
                                <circle id="peach" class="is-animated" cx="128" cy="320" r="40" fill="var(--logo-peach, #fab387)" />
                                <circle id="red" class="is-animated" cx="128" cy="192" r="40" fill="var(--logo-red, #f38ba8)" />
                            </svg>
                        @endif
                        <span class="mt-4 text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Logo Animation') }}</span>
                    </div>

                    <!-- UI Elements -->
                    <div class="theme-transition space-y-3 p-6 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50">
                        <flux:button variant="primary" class="w-full">{{ __('Primary Action') }}</flux:button>
                        <flux:button
                            class="w-full"
                            data-secondary-action
                        >
                            {{ __('Secondary Action') }}
                        </flux:button>
                        <flux:input placeholder="{{ __('Input field...') }}" />
                    </div>
                </div>

                <!-- Message Types Preview -->
                <div class="mt-4 space-y-3">
                    <flux:callout variant="secondary" color="blue" icon="information-circle">
                        <flux:callout.heading>{{ __('Info Message') }}</flux:callout.heading>
                        <flux:callout.text>{{ __('This demonstrates the Info accent color for informational messages.') }}</flux:callout.text>
                    </flux:callout>
                    <flux:callout variant="warning" icon="exclamation-triangle">
                        <flux:callout.heading>{{ __('Warning Message') }}</flux:callout.heading>
                        <flux:callout.text>{{ __('This demonstrates the Warning accent color for warning messages.') }}</flux:callout.text>
                    </flux:callout>
                    <flux:callout variant="danger" icon="x-circle">
                        <flux:callout.heading>{{ __('Error Message') }}</flux:callout.heading>
                        <flux:callout.text>{{ __('This demonstrates the Error accent color for error messages.') }}</flux:callout.text>
                    </flux:callout>
                    <flux:callout variant="success" icon="check-circle">
                        <flux:callout.heading>{{ __('Success Message') }}</flux:callout.heading>
                        <flux:callout.text>{{ __('This demonstrates the Success accent color for success messages.') }}</flux:callout.text>
                    </flux:callout>
                </div>
                </div>
            </div>

            @if($showReset)
                <div class="flex justify-end pt-2">
                    <flux:button
                        wire:click="resetToDefault"
                        wire:loading.attr="disabled"
                        icon="arrow-path"
                        variant="subtle"
                        size="sm"
                        data-test="reset-theme-button"
                    >
                        {{ __('Reset to Default') }}
                    </flux:button>
                </div>
            @endif

        </div>
    </x-settings.layout>
</section>
