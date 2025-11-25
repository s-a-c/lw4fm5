<!DOCTYPE html>
<html class="dark" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
@php
    $user = auth()->user();
    $themeFlavor = $user?->settings->flavor->value ?? 'mocha';
@endphp
<!-- DEBUG: themeFlavor type: {{ isset($themeFlavor) ? gettype($themeFlavor) : 'UNDEFINED' }} -->

    <flux:sidebar
        class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900"
        sticky
        stashable
        collapsible="desktop"
    >
        <div class="flex items-center justify-end gap-2">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <flux:sidebar.toggle
                class="hidden items-center justify-center rounded-lg text-zinc-500 transition hover:bg-zinc-800/5 hover:text-zinc-900 lg:flex lg:h-9 lg:w-9 dark:hover:bg-white/10 dark:hover:text-white"
                data-tooltip="Toggle sidebar" icon="arrows-right-left" aria-label="Toggle sidebar" />
        </div>

        <a class="flex items-center space-x-2 rtl:space-x-reverse" href="{{ route('dashboard') }}"
            wire:navigate>
            <x-app-logo />
        </a>

        <flux:sidebar.nav>
            <flux:sidebar.group heading="Platform">
                <flux:sidebar.item icon="home" :href="route('dashboard')"
                    :current="request()->routeIs('dashboard')" wire:navigate>
                    Dashboard
                </flux:sidebar.item>
            </flux:sidebar.group>
        </flux:sidebar.nav>

        <flux:sidebar.spacer />

        <flux:sidebar.nav>
            <flux:sidebar.item icon="folder-git-2"
                href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                Repository
            </flux:sidebar.item>

            <flux:sidebar.item icon="book-open-text"
                href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                Documentation
            </flux:sidebar.item>
        </flux:sidebar.nav>

        <flux:dropdown class="hidden lg:block" position="bottom" align="start">
            <flux:profile data-test="sidebar-menu-button" :name="$user?->name ?? 'Guest'"
                :initials="$user?->initials() ?? '??'" icon-trailing="chevron-up-down" />

            <flux:menu class="w-64">
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ $user?->initials() ?? '??' }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ $user?->name ?? 'Guest' }}</span>
                                <span class="truncate text-xs">{{ $user?->email ?? '' }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <div class="px-2 pb-2 pt-2">
                    <flux:radio.group x-data variant="segmented" x-model="$flux.appearance">
                        <flux:radio data-tooltip="Light" aria-label="Light" value="light" icon="sun" />
                        <flux:radio data-tooltip="Dark" aria-label="Dark" value="dark" icon="moon" />
                        <flux:radio data-tooltip="System" aria-label="System" value="system" icon="computer-desktop" />
                    </flux:radio.group>
                </div>

                <div class="px-2 pb-2" x-data="{
                    themeFlavor: 'mocha',
                    init() {
                        // Initialize with safe defaults
                        this.themeFlavor = 'mocha';

                        try {
                            // Try to get from local storage or document attribute
                            const storedFlavor = (typeof localStorage !== 'undefined' && localStorage.getItem('theme_flavor'));
                            const docFlavor = (typeof document !== 'undefined' && document.documentElement.getAttribute('data-flavor'));

                            if (storedFlavor) {
                                this.themeFlavor = storedFlavor;
                            } else if (docFlavor) {
                                this.themeFlavor = docFlavor;
                            }
                        } catch (e) {
                            // Fallback to mocha on error
                            this.themeFlavor = 'mocha';
                        }

                        // Ensure document attribute is synced
                        if (typeof document !== 'undefined' && document && document.documentElement) {
                            document.documentElement.setAttribute('data-flavor', this.themeFlavor);
                        }

                        $watch('themeFlavor', (value) => {
                            if (value) {
                                try {
                                    if (typeof localStorage !== 'undefined') {
                                        localStorage.setItem('theme_flavor', value);
                                    }
                                    if (typeof document !== 'undefined' && document && document.documentElement) {
                                        document.documentElement.setAttribute('data-flavor', value);
                                    }
                                    if (typeof $flux !== 'undefined' && $flux) {
                                        if (value === 'latte') {
                                            $flux.appearance = 'light';
                                        } else {
                                            $flux.appearance = 'dark';
                                        }
                                    }
                                } catch (e) {
                                    // Ignore errors in test environment
                                }
                            }
                        });
                    }
                }">
                    <flux:radio.group variant="segmented" name="theme_flavor" :value="$themeFlavor ?? 'mocha'" x-on:change="themeFlavor = $event.target.value">
                        <flux:radio data-tooltip="Latte" aria-label="Latte" value="latte" icon="sun" />
                        <flux:radio data-tooltip="Frappé" aria-label="Frappé" value="frappe" icon="cloud" />
                        <flux:radio data-tooltip="Macchiato" aria-label="Macchiato" value="macchiato" icon="cup-soda" />
                        <flux:radio data-tooltip="Mocha" aria-label="Mocha" value="mocha" icon="moon" />
                    </flux:radio.group>
                </div>

                <flux:menu.separator />

                <form class="w-full" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <flux:menu.item class="w-full" data-test="logout-button" as="button" type="submit"
                        icon="arrow-right-start-on-rectangle">
                        Log Out
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:sidebar>

    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="$user?->initials() ?? '??'" icon-trailing="chevron-down" />

            <flux:menu class="w-64">
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ $user?->initials() ?? '??' }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ $user?->name ?? 'Guest' }}</span>
                                <span class="truncate text-xs">{{ $user?->email ?? '' }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <div class="px-2 pb-2 pt-2">
                    <flux:radio.group x-data variant="segmented" x-model="$flux.appearance">
                        <flux:radio data-tooltip="Light" aria-label="Light" value="light" icon="sun" />
                        <flux:radio data-tooltip="Dark" aria-label="Dark" value="dark" icon="moon" />
                        <flux:radio data-tooltip="System" aria-label="System" value="system" icon="computer-desktop" />
                    </flux:radio.group>
                </div>

                <div class="px-2 pb-2" x-data="{
                    themeFlavor: 'mocha',
                    init() {
                        // Initialize with safe defaults
                        this.themeFlavor = 'mocha';

                        try {
                            // Try to get from local storage or document attribute
                            const storedFlavor = (typeof localStorage !== 'undefined' && localStorage.getItem('theme_flavor'));
                            const docFlavor = (typeof document !== 'undefined' && document.documentElement.getAttribute('data-flavor'));

                            if (storedFlavor) {
                                this.themeFlavor = storedFlavor;
                            } else if (docFlavor) {
                                this.themeFlavor = docFlavor;
                            }
                        } catch (e) {
                            // Fallback to mocha on error
                            this.themeFlavor = 'mocha';
                        }

                        // Ensure document attribute is synced
                        if (typeof document !== 'undefined' && document && document.documentElement) {
                            document.documentElement.setAttribute('data-flavor', this.themeFlavor);
                        }

                        $watch('themeFlavor', (value) => {
                            if (value) {
                                try {
                                    if (typeof localStorage !== 'undefined') {
                                        localStorage.setItem('theme_flavor', value);
                                    }
                                    if (typeof document !== 'undefined' && document && document.documentElement) {
                                        document.documentElement.setAttribute('data-flavor', value);
                                    }
                                    if (typeof $flux !== 'undefined' && $flux) {
                                        if (value === 'latte') {
                                            $flux.appearance = 'light';
                                        } else {
                                            $flux.appearance = 'dark';
                                        }
                                    }
                                } catch (e) {
                                    // Ignore errors in test environment
                                }
                            }
                        });
                    }
                }">
                    <flux:radio.group variant="segmented" name="theme_flavor" :value="$themeFlavor ?? 'mocha'" x-on:change="themeFlavor = $event.target.value">
                        <flux:radio data-tooltip="Latte" aria-label="Latte" value="latte" icon="sun" />
                        <flux:radio data-tooltip="Frappé" aria-label="Frappé" value="frappe" icon="cloud" />
                        <flux:radio data-tooltip="Macchiato" aria-label="Macchiato" value="macchiato" icon="cup-soda" />
                        <flux:radio data-tooltip="Mocha" aria-label="Mocha" value="mocha" icon="moon" />
                    </flux:radio.group>
                </div>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>Settings
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form class="w-full" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <flux:menu.item class="w-full" data-test="logout-button" as="button" type="submit"
                        icon="arrow-right-start-on-rectangle">
                        Log Out
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}

    @livewireScripts
    @filamentScripts
    @fluxScripts
    <script nonce="{{ csp_nonce() }}">
        console.log("I am secure!");
    </script>
</body>

</html>
