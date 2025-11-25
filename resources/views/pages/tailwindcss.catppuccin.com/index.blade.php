@php
    use function Laravel\Folio\name;

    name('tailwindcss.catppuccin.flux');
@endphp
<!DOCTYPE html>

<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width">
        <link rel="icon" type="image/svg+xml" href="{{ asset('tailwindcss-catppuccin/favicon.svg') }}">
        <meta name="generator" content="Astro v5.12.3">
        <script>
            const isThemeAuto = localStorage.getItem("theme-auto");
            if (isThemeAuto === null) {
                const prefersDark = window.matchMedia(
                    "(prefers-color-scheme: dark)"
                ).matches;
                const defaultTheme = prefersDark ? "mocha" : "latte";
                localStorage.setItem("theme", defaultTheme);
                localStorage.setItem("theme-auto", "true");
                document.documentElement.className = defaultTheme;
            }
            const theme = localStorage.getItem("theme");
            if (theme) {
                document.documentElement.className = theme;
            }
        </script>
        <title>Catppuccin × tailwindcss</title>
        @vite(['resources/css/app.css'])
        @livewireStyles
    </head>

    <body class="bg-ctp-base">
        <div class="relative grid min-h-screen w-screen place-items-center overflow-clip">
            <div class="background bg-linear-35 from-ctp-mantle to-ctp-base absolute inset-0">
                @php
                    $backgroundWaveSvg = file_get_contents(public_path('images/catppuccin/background-wave.svg'));
                    // Inject classes into SVG (preserve existing path fill classes)
                    $backgroundWaveSvg = preg_replace('/<svg\s+/', '<svg class="origin-bottom-center scale-120 scale-x-200 absolute bottom-0 right-0 translate-x-[50%] translate-y-[60%] rotate-[-20deg] transform" ', $backgroundWaveSvg, 1);
                    // Ensure path elements have their fill classes (they already exist in the SVG file)
                    echo $backgroundWaveSvg;
                @endphp
            </div>
            <div class="hero grid h-screen w-full max-w-[1800px] gap-8 p-6 md:p-12">
                <div class="pepperjack z-10 h-12">
                    @php
                        $logoSvg = file_get_contents(public_path('images/catppuccin/pepperjack-logo.svg'));
                        // Inject classes into SVG
                        $logoSvg = preg_replace('/<svg\s+/', '<svg class="text-ctp-mantle dark:text-ctp-crust" ', $logoSvg, 1);
                        echo $logoSvg;
                    @endphp
                </div>
                <div class="blurb z-10 mt-16 font-bold md:mt-0 md:self-center">
                    <h1 class="bg-linear-35 from-ctp-primary to-ctp-secondary bg-clip-text text-4xl text-transparent 2xl:text-5xl">
                        Catppuccin
                        <span class="text-ctp-text">×</span> tailwindcss
                    </h1>
                    <div class="text-ctp-text mt-8 2xl:text-xl">
                        <p>Add Catppuccin's soothing pastel theme to your Tailwind CSS projects!
                            Follow the instructions at <a class="text-ctp-blue hover:text-ctp-blue-700"
                                href="https://github.com/catppuccin/tailwindcss">catppuccin/tailwindcss</a> to get
                            started.
                        </p>
                    </div>
                    {{--
                      FLUX DEVIATION: flux:radio.group variant="segmented"
                      Border Radius: 2px (default ~6-8px)
                      Backgrounds: Custom Catppuccin gradients (default: solid colors)
                      Text: text-ctp-crust (default: theme colors)
                      Spacing: gap-2 flex-wrap (default: single row)
                      Padding: px-4 py-2 (default: smaller)
                      Text Size: text-sm 2xl:text-lg (default: standard)
                      Font Weight: font-semibold (default: medium)
                    --}}
                    <div id="flavour-switcher" class="mt-6"
                         x-data="{
                             selectedTheme: (() => {
                                 const isAuto = localStorage.getItem('theme-auto') === 'true';
                                 if (isAuto) return 'auto';
                                 return localStorage.getItem('theme') || 'latte';
                             })(),
                             isDarkTheme(theme) {
                                 return ['frappe', 'macchiato', 'mocha'].includes(theme);
                             },
                             handleChange() {
                                 const theme = this.selectedTheme;
                                 let finalTheme;

                                 if (theme === 'auto') {
                                     const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                                     finalTheme = prefersDark ? 'mocha' : 'latte';
                                     localStorage.setItem('theme-auto', 'true');
                                 } else {
                                     finalTheme = theme;
                                     localStorage.setItem('theme', theme);
                                     localStorage.setItem('theme-auto', 'false');
                                 }

                                 // Apply theme class and dark class for dark themes
                                 document.documentElement.className = finalTheme;
                                 if (this.isDarkTheme(finalTheme)) {
                                     document.documentElement.classList.add('dark');
                                 } else {
                                     document.documentElement.classList.remove('dark');
                                 }
                             }
                         }"
                         x-init="
                             handleChange();
                             $watch('selectedTheme', () => handleChange());
                             // Listen for system theme changes when in auto mode
                             window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                                 if (this.selectedTheme === 'auto') {
                                     handleChange();
                                 }
                             });
                         ">
                        <flux:radio.group
                            variant="segmented"
                            x-model="selectedTheme"
                            class="flex flex-wrap gap-2 [&_[data-flux-radio]]:rounded-[2px] [&_[data-flux-radio]]:px-4 [&_[data-flux-radio]]:py-2 [&_[data-flux-radio]]:text-sm [&_[data-flux-radio]]:font-semibold [&_[data-flux-radio]]:text-ctp-crust [&_[data-flux-radio]]:transition-colors [&_[data-flux-radio]]:cursor-pointer 2xl:[&_[data-flux-radio]]:text-lg">
                            <flux:radio value="latte" id="latte">🌻 Latte</flux:radio>
                            <flux:radio value="frappe" id="frappe">🪴 Frappé</flux:radio>
                            <flux:radio value="macchiato" id="macchiato">🌺 Macchiato</flux:radio>
                            <flux:radio value="mocha" id="mocha">🌿 Mocha</flux:radio>
                            <flux:radio value="auto" id="auto">🖥️ Auto</flux:radio>
                        </flux:radio.group>
                    </div>
                    {{-- FLUX DEVIATION: Custom CSS for Catppuccin gradient backgrounds --}}
                    <style>
                        /*
                          FLUX DEVIATION: Custom Catppuccin gradient backgrounds for radio buttons
                          Uses CSS variables with oklch fallbacks for colors
                          Gradient angle: 35deg (matches bg-linear-35 utility)
                          Selector: ui-radio (Flux renders radio buttons as <ui-radio> elements)
                        */

                        /* Base styles for all radio buttons */
                        #flavour-switcher ui-radio {
                            border-radius: 2px !important;
                        }

                        /* Base styles for unchecked radio buttons - primary to secondary gradient */
                        #flavour-switcher ui-radio:not([data-checked]) {
                            background: linear-gradient(35deg,
                                var(--color-ctp-primary-400, oklch(0.65 0.19 264)),
                                var(--color-ctp-secondary-400, oklch(0.66 0.15 220))
                            ) !important;
                            color: var(--color-ctp-crust) !important;
                        }

                        /* Hover state for unchecked radio buttons */
                        #flavour-switcher ui-radio:not([data-checked]):hover {
                            background: linear-gradient(35deg,
                                var(--color-ctp-primary-600, oklch(0.52 0.22 264)),
                                var(--color-ctp-secondary-600, oklch(0.53 0.18 220))
                            ) !important;
                        }

                        /* Checked state - mauve to blue gradient */
                        #flavour-switcher ui-radio[data-checked] {
                            background: linear-gradient(35deg,
                                var(--color-ctp-mauve-400, oklch(0.64 0.19 300)),
                                var(--color-ctp-blue-400, oklch(0.65 0.19 264))
                            ) !important;
                            color: var(--color-ctp-crust) !important;
                        }

                        /* Hover state for checked radio buttons */
                        #flavour-switcher ui-radio[data-checked]:hover {
                            background: linear-gradient(35deg,
                                var(--color-ctp-mauve-600, oklch(0.58 0.23 300)),
                                var(--color-ctp-blue-600, oklch(0.52 0.22 264))
                            ) !important;
                        }
                    </style>
                    <script>
                        // Handle prefers-color-scheme changes when auto mode is enabled
                        const prefersDark = window.matchMedia("(prefers-color-scheme: dark)");
                        prefersDark.addEventListener("change", (event) => {
                            const isThemeAuto = localStorage.getItem("theme-auto");
                            if (isThemeAuto === "true") {
                                const defaultTheme = event.matches ? "mocha" : "latte";
                                document.documentElement.className = defaultTheme;
                                localStorage.setItem("theme", defaultTheme);
                            }
                        });
                    </script>
                </div>
                <div class="example grid place-items-center">
                    <div class="transform-wrapper tranform rotate-[-2deg]">
                        <div id="calendar-wrapper">
                            <noscript>
                                <div class="bg-ctp-base text-ctp-text 2xl:scale-140 overflow-clip rounded-lg shadow-lg 2xl:transform"
                                    id="calendar">
                                    <div
                                        class="bg-ctp-crust bg-linear-30 from-ctp-crust to-ctp-mantle text-ctp-text relative px-8 py-4 font-bold">
                                        April
                                        <div
                                            class="bg-ctp-primary bg-linear-30 from-ctp-primary to-ctp-secondary absolute bottom-0 left-0 right-0 h-1">
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-7 grid-rows-6 gap-2 p-6">
                                        <div class="legend col-span-7 grid grid-cols-subgrid">
                                            <div
                                                class="rounded-xs text-ctp-overlay0 grid h-8 w-8 place-items-center text-center">
                                                S
                                            </div>
                                            <div
                                                class="rounded-xs text-ctp-overlay0 grid h-8 w-8 place-items-center text-center">
                                                M
                                            </div>
                                            <div
                                                class="rounded-xs text-ctp-overlay0 grid h-8 w-8 place-items-center text-center">
                                                T
                                            </div>
                                            <div
                                                class="rounded-xs text-ctp-overlay0 grid h-8 w-8 place-items-center text-center">
                                                W
                                            </div>
                                            <div
                                                class="rounded-xs text-ctp-overlay0 grid h-8 w-8 place-items-center text-center">
                                                T
                                            </div>
                                            <div
                                                class="rounded-xs text-ctp-overlay0 grid h-8 w-8 place-items-center text-center">
                                                F
                                            </div>
                                            <div
                                                class="rounded-xs text-ctp-overlay0 grid h-8 w-8 place-items-center text-center">
                                                S
                                            </div>
                                        </div>
                                        <div class="week col-span-7 grid grid-cols-subgrid">
                                            <div
                                                class="rounded-xs hover:bg-ctp-crust text-ctp-surface0 grid h-8 w-8 place-items-center text-center">
                                                30
                                            </div>
                                            <div
                                                class="rounded-xs hover:bg-ctp-crust text-ctp-surface0 grid h-8 w-8 place-items-center text-center">
                                                31
                                            </div>
                                            <div
                                                class="rounded-xs hover:bg-ctp-crust text-ctp-text grid h-8 w-8 place-items-center text-center">
                                                1
                                            </div>
                                            <div
                                                class="rounded-xs hover:bg-ctp-crust text-ctp-text grid h-8 w-8 place-items-center text-center">
                                                2
                                            </div>
                                            <div
                                                class="rounded-xs hover:bg-ctp-crust text-ctp-text grid h-8 w-8 place-items-center text-center">
                                                3
                                            </div>
                                            <div
                                                class="rounded-xs bg-ctp-primary-300 dark:bg-ctp-primary-700 bg-linear-30 from from-ctp-sapphire-300 to-ctp-secondary-300 dark:from-ctp-sapphire-700 dark:to-ctp-secondary-700 text-ctp-base grid h-8 w-8 place-items-center text-center font-bold">
                                                4
                                            </div>
                                            <div
                                                class="rounded-xs hover:bg-ctp-crust text-ctp-overlay0 grid h-8 w-8 place-items-center text-center">
                                                5
                                            </div>
                                        </div>
                                        <div
                                            class="week rounded-xs has-[.today]:bg-ctp-primary-50 has-[.today]:dark:bg-ctp-primary-950 has-[.today]:bg-linear-30 from-ctp-primary-50 to-ctp-secondary-50 dark:from-ctp-primary-950 dark:to-ctp-secondary-950 col-span-7 grid grid-cols-subgrid">
                                            <div
                                                class="rounded-xs hover:bg-ctp-primary-100 dark:hover:bg-ctp-primary-900 text-ctp-subtext0 grid h-8 w-8 place-items-center text-center">
                                                6
                                            </div>
                                            <div
                                                class="rounded-xs hover:bg-ctp-primary-100 dark:hover:bg-ctp-primary-900 text-ctp-text grid h-8 w-8 place-items-center text-center">
                                                7
                                            </div>
                                            <div
                                                class="rounded-xs hover:bg-ctp-primary-100 dark:hover:bg-ctp-primary-900 text-ctp-text grid h-8 w-8 place-items-center text-center">
                                                8
                                            </div>
                                            <div
                                                class="rounded-xs hover:bg-ctp-primary-100 dark:hover:bg-ctp-primary-900 text-ctp-text grid h-8 w-8 place-items-center text-center">
                                                9
                                            </div>
                                            <div
                                                class="today rounded-xs bg-ctp-primary bg-linear-30 from-ctp-primary to-ctp-secondary text-ctp-base dark:text-ctp-crust relative grid h-8 w-8 place-items-center text-center font-bold">
                                                <span class="z-10">10</span>
                                                <div
                                                    class="rounded-xs bg-ctp-primary bg-linear-30 from-ctp-primary to-ctp-secondary absolute h-6 w-6 animate-ping opacity-50">
                                                </div>
                                            </div>
                                            <div
                                                class="rounded-xs hover:bg-ctp-primary-100 dark:hover:bg-ctp-primary-900 text-ctp-text grid h-8 w-8 place-items-center text-center">
                                                11
                                            </div>
                                            <div
                                                class="rounded-xs hover:bg-ctp-primary-100 dark:hover:bg-ctp-primary-900 text-ctp-subtext0 grid h-8 w-8 place-items-center text-center">
                                                12
                                            </div>
                                        </div>
                                        <div class="week col-span-7 grid grid-cols-subgrid">
                                            <div
                                                class="rounded-xs bg-ctp-primary-300 dark:bg-ctp-primary-700 bg-linear-30 from from-ctp-sapphire-300 to-ctp-secondary-300 dark:from-ctp-sapphire-700 dark:to-ctp-secondary-700 text-ctp-base grid h-8 w-8 place-items-center text-center font-bold">
                                                13
                                            </div>
                                            <div
                                                class="rounded-xs hover:bg-ctp-crust text-ctp-text grid h-8 w-8 place-items-center text-center">
                                                14
                                            </div>
                                            <div
                                                class="rounded-xs hover:bg-ctp-crust text-ctp-text grid h-8 w-8 place-items-center text-center">
                                                15
                                            </div>
                                            <div
                                                class="rounded-xs hover:bg-ctp-crust text-ctp-text grid h-8 w-8 place-items-center text-center">
                                                16
                                            </div>
                                            <div
                                                class="rounded-xs hover:bg-ctp-crust text-ctp-text grid h-8 w-8 place-items-center text-center">
                                                17
                                            </div>
                                            <div
                                                class="rounded-xs hover:bg-ctp-crust text-ctp-text grid h-8 w-8 place-items-center text-center">
                                                18
                                            </div>
                                            <div
                                                class="rounded-xs hover:bg-ctp-crust text-ctp-overlay0 grid h-8 w-8 place-items-center text-center">
                                                19
                                            </div>
                                            <div
                                                class="rounded-xs hover:bg-ctp-crust text-ctp-overlay0 grid h-8 w-8 place-items-center text-center">
                                                20
                                            </div>
                                            <div
                                                class="rounded-xs hover:bg-ctp-crust text-ctp-overlay0 grid h-8 w-8 place-items-center text-center">
                                                21
                                            </div>
                                            <div
                                                class="rounded-xs hover:bg-ctp-crust text-ctp-text grid h-8 w-8 place-items-center text-center">
                                                22
                                            </div>
                                            <div
                                                class="rounded-xs bg-ctp-mauve-300 dark:bg-ctp-mauve-700 bg-linear-30 from from-ctp-mauve-300 to-ctp-pink-300 dark:from-ctp-mauve-700 dark:to-ctp-pink-700 text-ctp-base grid h-8 w-8 place-items-center text-center font-bold">
                                                23
                                            </div>
                                            <div
                                                class="rounded-xs hover:bg-ctp-crust text-ctp-text grid h-8 w-8 place-items-center text-center">
                                                24
                                            </div>
                                            <div
                                                class="rounded-xs hover:bg-ctp-crust text-ctp-text grid h-8 w-8 place-items-center text-center">
                                                25
                                            </div>
                                            <div
                                                class="rounded-xs hover:bg-ctp-crust text-ctp-overlay0 grid h-8 w-8 place-items-center text-center">
                                                26
                                            </div>
                                        </div>
                                        <div class="week col-span-7 grid grid-cols-subgrid">
                                            <div
                                                class="rounded-xs hover:bg-ctp-crust text-ctp-overlay0 grid h-8 w-8 place-items-center text-center">
                                                27
                                            </div>
                                            <div
                                                class="rounded-xs bg-ctp-rosewater-300 dark:bg-ctp-rosewater-700 bg-linear-30 from from-ctp-flamingo-300 to-ctp-rosewater-300 dark:from-ctp-flamingo-600 dark:to-ctp-rosewater-600 text-ctp-base grid h-8 w-8 place-items-center text-center font-bold">
                                                28
                                            </div>
                                            <div
                                                class="rounded-xs hover:bg-ctp-crust text-ctp-text grid h-8 w-8 place-items-center text-center">
                                                29
                                            </div>
                                            <div
                                                class="rounded-xs hover:bg-ctp-crust text-ctp-text grid h-8 w-8 place-items-center text-center">
                                                30
                                            </div>
                                            <div
                                                class="rounded-xs hover:bg-ctp-crust text-ctp-surface0 grid h-8 w-8 place-items-center text-center">
                                                1
                                            </div>
                                            <div
                                                class="rounded-xs hover:bg-ctp-crust text-ctp-surface0 grid h-8 w-8 place-items-center text-center">
                                                2
                                            </div>
                                            <div
                                                class="rounded-xs hover:bg-ctp-crust text-ctp-surface0 grid h-8 w-8 place-items-center text-center">
                                                3
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </noscript>
                        </div>
                        <template id="calendar-template">
                            <div class="bg-ctp-base text-ctp-text 2xl:scale-140 overflow-clip rounded-lg shadow-lg 2xl:transform"
                                id="calendar">
                                <div
                                    class="bg-ctp-crust bg-linear-30 from-ctp-crust to-ctp-mantle text-ctp-text relative px-8 py-4 font-bold">
                                    <span id="month-name"></span>
                                    <div
                                        class="bg-ctp-blue bg-linear-30 from-ctp-primary to-ctp-secondary absolute bottom-0 left-0 right-0 h-1">
                                    </div>
                                </div>
                                <div class="grid grid-cols-7 gap-2 p-6" id="calendar-page">
                                    <div class="legend col-span-7 grid grid-cols-subgrid">
                                        <div
                                            class="rounded-xs text-ctp-overlay0 grid h-8 w-8 place-items-center text-center">
                                            S
                                        </div>
                                        <div
                                            class="rounded-xs text-ctp-overlay0 grid h-8 w-8 place-items-center text-center">
                                            M
                                        </div>
                                        <div
                                            class="rounded-xs text-ctp-overlay0 grid h-8 w-8 place-items-center text-center">
                                            T
                                        </div>
                                        <div
                                            class="rounded-xs text-ctp-overlay0 grid h-8 w-8 place-items-center text-center">
                                            W
                                        </div>
                                        <div
                                            class="rounded-xs text-ctp-overlay0 grid h-8 w-8 place-items-center text-center">
                                            T
                                        </div>
                                        <div
                                            class="rounded-xs text-ctp-overlay0 grid h-8 w-8 place-items-center text-center">
                                            F
                                        </div>
                                        <div
                                            class="rounded-xs text-ctp-overlay0 grid h-8 w-8 place-items-center text-center">
                                            S
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template id="week-template">
                            <div
                                class="week rounded-xs has-[.today]:bg-ctp-primary-50 has-[.today]:dark:bg-ctp-primary-950 has-[.today]:bg-linear-30 from-ctp-primary-50 to-ctp-secondary-50 dark:from-ctp-primary-950 dark:to-ctp-secondary-950 col-span-7 grid grid-cols-subgrid">
                            </div>
                        </template>
                        <template id="regular-day-template">
                            <div
                                class="rounded-xs hover:bg-ctp-primary/35 text-ctp-text grid h-8 w-8 place-items-center text-center">
                                #date#
                            </div>
                        </template>
                        <template id="current-day-template">
                            <div
                                class="today rounded-xs bg-ctp-primary bg-linear-30 from-ctp-primary to-ctp-secondary text-ctp-base dark:text-ctp-crust grid h-8 w-8 place-items-center text-center font-bold">
                                #date#
                            </div>
                        </template>
                        <template id="muted-day-template">
                            <div
                                class="rounded-xs hover:bg-ctp-primary/35 text-ctp-overlay0 grid h-8 w-8 place-items-center text-center">
                                #date#
                            </div>
                        </template>
                        <script type="module">
                            function u(a, r) {
                                const o = a.content.cloneNode(!0),
                                    l = o.querySelector("div");
                                return l.textContent = r, o
                            }

                            function C(a, r, o) {
                                const l = [];
                                if (a.firstDayWeekday != 0)
                                    for (let t = 0; t < a.firstDayWeekday; t++) {
                                        const e = document.getElementById("muted-day-template");
                                        if (!e) return console.error("Muted day template not found");
                                        const n = u(e, +r.length - t);
                                        l.push(n)
                                    }
                                for (let t = 1; t <= +a.length; t++) {
                                    const e = t == o.number ? "current-day-template" : "regular-day-template",
                                        n = document.getElementById(e);
                                    if (!n) return console.error(`${e} not found`);
                                    const d = u(n, t);
                                    l.push(d)
                                }
                                if (a.lastDayWeekday != 6)
                                    for (let t = 0; t < 6 - a.lastDayWeekday; t++) {
                                        const e = document.getElementById("muted-day-template");
                                        if (!e) return console.error("Muted day template not found");
                                        const n = u(e, t + 1);
                                        l.push(n)
                                    }
                                return l
                            }

                            function k(a) {
                                const r = [];
                                for (let o = 0; o < a.length; o += 7) {
                                    const l = document.getElementById("week-template");
                                    if (l) {
                                        const t = l.content.cloneNode(!0),
                                            e = t.querySelector("div");
                                        for (let n = 0; n < 7; n++) e.appendChild(a[o + n]);
                                        r.push(t)
                                    }
                                }
                                return r
                            }(function(r) {
                                const o = (c, h) => new Date(c, h + 1, 0).getDate(),
                                    l = c => c.toLocaleString("default", {
                                        month: "long"
                                    }),
                                    t = c => c.toLocaleString("default", {
                                        weekday: "long"
                                    }),
                                    e = r,
                                    n = e.getFullYear(),
                                    d = {
                                        number: e.getMonth(),
                                        name: l(e),
                                        length: o(n, e.getMonth()),
                                        firstDayWeekday: new Date(n, e.getMonth(), 1).getDay(),
                                        firstDayName: t(new Date(n, e.getMonth(), 1)),
                                        lastDayWeekday: new Date(e.getFullYear(), e.getMonth() + 1, 0).getDay()
                                    },
                                    g = {
                                        number: e.getMonth() - 1,
                                        length: o(n, e.getMonth() - 1)
                                    },
                                    p = {
                                        number: e.getDate(),
                                        name: t(e)
                                    },
                                    i = C(d, g, p),
                                    f = k(i),
                                    m = document.getElementById("calendar-template");
                                if (!m) return console.error("Calendar template not found");
                                const s = m.content.cloneNode(!0),
                                    y = s.querySelector("#month-name");
                                y && (y.textContent = d.name);
                                const D = s.querySelector("#calendar-page");
                                f.forEach(c => {
                                    D?.appendChild(c)
                                }), document.getElementById("calendar-wrapper")?.appendChild(s)
                            })(new Date);
                        </script>
                    </div>
                </div>
            </div>
        </div>
        <div class="latte bg-ctp-base grid place-items-center p-3 md:p-12">
            <h2 class="text-2xl font-bold">
                🌻
                <span class="bg-linear-35 from-ctp-yellow to-ctp-peach bg-clip-text text-transparent">
                    Latte
                </span>
            </h2>
            <table class="mt-3 border-separate border-spacing-1 sm:border-spacing-2">
                <tr class="*:[writing-mode:vertical-lr] sm:*:[writing-mode:horizontal-tb]">
                    <td></td>
                    <td
                        class="text-ctp-subtext0 rotate-180 transform text-left font-mono text-xs font-normal sm:rotate-0 sm:text-center">
                        50</td>
                    <td
                        class="text-ctp-subtext0 rotate-180 transform text-left font-mono text-xs font-normal sm:rotate-0 sm:text-center">
                        100</td>
                    <td
                        class="text-ctp-subtext0 rotate-180 transform text-left font-mono text-xs font-normal sm:rotate-0 sm:text-center">
                        200</td>
                    <td
                        class="text-ctp-subtext0 rotate-180 transform text-left font-mono text-xs font-normal sm:rotate-0 sm:text-center">
                        300</td>
                    <td
                        class="text-ctp-subtext0 rotate-180 transform text-left font-mono text-xs font-normal sm:rotate-0 sm:text-center">
                        400</td>
                    <td
                        class="text-ctp-subtext0 rotate-180 transform text-left font-mono text-xs font-normal sm:rotate-0 sm:text-center">
                        500</td>
                    <td
                        class="text-ctp-subtext0 rotate-180 transform text-left font-mono text-xs font-normal sm:rotate-0 sm:text-center">
                        600</td>
                    <td
                        class="text-ctp-subtext0 rotate-180 transform text-left font-mono text-xs font-normal sm:rotate-0 sm:text-center">
                        700</td>
                    <td
                        class="text-ctp-subtext0 rotate-180 transform text-left font-mono text-xs font-normal sm:rotate-0 sm:text-center">
                        800</td>
                    <td
                        class="text-ctp-subtext0 rotate-180 transform text-left font-mono text-xs font-normal sm:rotate-0 sm:text-center">
                        900</td>
                    <td
                        class="text-ctp-subtext0 rotate-180 transform text-left font-mono text-xs font-normal sm:rotate-0 sm:text-center">
                        950</td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Rosewater</th>
                    <td>
                        <div
                            class="bg-ctp-rosewater-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-rosewater-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-rosewater-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-rosewater-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-rosewater-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-rosewater-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-rosewater-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-rosewater-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-rosewater-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-rosewater-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-rosewater-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Flamingo</th>
                    <td>
                        <div
                            class="bg-ctp-flamingo-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-flamingo-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-flamingo-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-flamingo-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-flamingo-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-flamingo-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-flamingo-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-flamingo-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-flamingo-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-flamingo-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-flamingo-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Pink</th>
                    <td>
                        <div
                            class="bg-ctp-pink-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-pink-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-pink-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-pink-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-pink-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-pink-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-pink-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-pink-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-pink-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-pink-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-pink-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Mauve</th>
                    <td>
                        <div
                            class="bg-ctp-mauve-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-mauve-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-mauve-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-mauve-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-mauve-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-mauve-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-mauve-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-mauve-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-mauve-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-mauve-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-mauve-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Red</th>
                    <td>
                        <div class="bg-ctp-red-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-red-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-red-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-red-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-red-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-red-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-red-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-red-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-red-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-red-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-red-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Maroon</th>
                    <td>
                        <div
                            class="bg-ctp-maroon-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-maroon-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-maroon-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-maroon-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-maroon-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-maroon-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-maroon-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-maroon-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-maroon-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-maroon-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-maroon-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Peach</th>
                    <td>
                        <div
                            class="bg-ctp-peach-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-peach-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-peach-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-peach-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-peach-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-peach-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-peach-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-peach-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-peach-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-peach-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-peach-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Yellow</th>
                    <td>
                        <div
                            class="bg-ctp-yellow-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-yellow-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-yellow-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-yellow-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-yellow-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-yellow-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-yellow-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-yellow-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-yellow-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-yellow-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-yellow-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Green</th>
                    <td>
                        <div
                            class="bg-ctp-green-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-green-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-green-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-green-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-green-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-green-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-green-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-green-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-green-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-green-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-green-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Teal</th>
                    <td>
                        <div
                            class="bg-ctp-teal-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-teal-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-teal-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-teal-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-teal-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-teal-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-teal-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-teal-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-teal-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-teal-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-teal-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Sky</th>
                    <td>
                        <div class="bg-ctp-sky-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sky-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sky-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sky-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sky-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sky-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sky-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sky-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sky-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sky-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sky-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Sapphire</th>
                    <td>
                        <div
                            class="bg-ctp-sapphire-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sapphire-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sapphire-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sapphire-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sapphire-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sapphire-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sapphire-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sapphire-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sapphire-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sapphire-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sapphire-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Blue</th>
                    <td>
                        <div
                            class="bg-ctp-blue-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-blue-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-blue-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-blue-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-blue-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-blue-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-blue-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-blue-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-blue-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-blue-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-blue-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Lavender</th>
                    <td>
                        <div
                            class="bg-ctp-lavender-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-lavender-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-lavender-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-lavender-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-lavender-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-lavender-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-lavender-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-lavender-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-lavender-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-lavender-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-lavender-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="frappe bg-ctp-base grid place-items-center p-3 md:p-12">
            <h2 class="text-2xl font-bold">
                🪴
                <span class="bg-linear-35 from-ctp-rosewater to-ctp-flamingo bg-clip-text text-transparent">
                    Frappé
                </span>
            </h2>
            <table class="mt-3 border-separate border-spacing-1 sm:border-spacing-2">
                <tr class="*:[writing-mode:vertical-lr] sm:*:[writing-mode:horizontal-tb]">
                    <td></td>
                    <td
                        class="text-ctp-subtext0 rotate-180 transform text-left font-mono text-xs font-normal sm:rotate-0 sm:text-center">
                        50</td>
                    <td
                        class="text-ctp-subtext0 rotate-180 transform text-left font-mono text-xs font-normal sm:rotate-0 sm:text-center">
                        100</td>
                    <td
                        class="text-ctp-subtext0 rotate-180 transform text-left font-mono text-xs font-normal sm:rotate-0 sm:text-center">
                        200</td>
                    <td
                        class="text-ctp-subtext0 rotate-180 transform text-left font-mono text-xs font-normal sm:rotate-0 sm:text-center">
                        300</td>
                    <td
                        class="text-ctp-subtext0 rotate-180 transform text-left font-mono text-xs font-normal sm:rotate-0 sm:text-center">
                        400</td>
                    <td
                        class="text-ctp-subtext0 rotate-180 transform text-left font-mono text-xs font-normal sm:rotate-0 sm:text-center">
                        500</td>
                    <td
                        class="text-ctp-subtext0 rotate-180 transform text-left font-mono text-xs font-normal sm:rotate-0 sm:text-center">
                        600</td>
                    <td
                        class="text-ctp-subtext0 rotate-180 transform text-left font-mono text-xs font-normal sm:rotate-0 sm:text-center">
                        700</td>
                    <td
                        class="text-ctp-subtext0 rotate-180 transform text-left font-mono text-xs font-normal sm:rotate-0 sm:text-center">
                        800</td>
                    <td
                        class="text-ctp-subtext0 rotate-180 transform text-left font-mono text-xs font-normal sm:rotate-0 sm:text-center">
                        900</td>
                    <td
                        class="text-ctp-subtext0 rotate-180 transform text-left font-mono text-xs font-normal sm:rotate-0 sm:text-center">
                        950</td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Rosewater</th>
                    <td>
                        <div
                            class="bg-ctp-rosewater-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-rosewater-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-rosewater-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-rosewater-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-rosewater-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-rosewater-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-rosewater-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-rosewater-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-rosewater-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-rosewater-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-rosewater-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Flamingo</th>
                    <td>
                        <div
                            class="bg-ctp-flamingo-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-flamingo-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-flamingo-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-flamingo-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-flamingo-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-flamingo-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-flamingo-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-flamingo-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-flamingo-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-flamingo-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-flamingo-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Pink</th>
                    <td>
                        <div
                            class="bg-ctp-pink-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-pink-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-pink-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-pink-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-pink-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-pink-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-pink-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-pink-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-pink-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-pink-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-pink-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Mauve</th>
                    <td>
                        <div
                            class="bg-ctp-mauve-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-mauve-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-mauve-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-mauve-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-mauve-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-mauve-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-mauve-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-mauve-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-mauve-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-mauve-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-mauve-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Red</th>
                    <td>
                        <div class="bg-ctp-red-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-red-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-red-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-red-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-red-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-red-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-red-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-red-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-red-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-red-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-red-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Maroon</th>
                    <td>
                        <div
                            class="bg-ctp-maroon-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-maroon-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-maroon-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-maroon-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-maroon-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-maroon-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-maroon-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-maroon-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-maroon-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-maroon-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-maroon-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Peach</th>
                    <td>
                        <div
                            class="bg-ctp-peach-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-peach-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-peach-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-peach-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-peach-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-peach-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-peach-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-peach-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-peach-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-peach-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-peach-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Yellow</th>
                    <td>
                        <div
                            class="bg-ctp-yellow-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-yellow-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-yellow-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-yellow-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-yellow-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-yellow-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-yellow-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-yellow-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-yellow-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-yellow-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-yellow-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Green</th>
                    <td>
                        <div
                            class="bg-ctp-green-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-green-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-green-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-green-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-green-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-green-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-green-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-green-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-green-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-green-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-green-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Teal</th>
                    <td>
                        <div
                            class="bg-ctp-teal-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-teal-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-teal-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-teal-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-teal-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-teal-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-teal-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-teal-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-teal-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-teal-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-teal-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Sky</th>
                    <td>
                        <div class="bg-ctp-sky-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sky-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sky-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sky-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sky-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sky-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sky-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sky-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sky-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sky-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sky-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Sapphire</th>
                    <td>
                        <div
                            class="bg-ctp-sapphire-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sapphire-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sapphire-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sapphire-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sapphire-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sapphire-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sapphire-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sapphire-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sapphire-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sapphire-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sapphire-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Blue</th>
                    <td>
                        <div
                            class="bg-ctp-blue-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-blue-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-blue-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-blue-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-blue-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-blue-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-blue-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-blue-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-blue-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-blue-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-blue-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Lavender</th>
                    <td>
                        <div
                            class="bg-ctp-lavender-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-lavender-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-lavender-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-lavender-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-lavender-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-lavender-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-lavender-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-lavender-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-lavender-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-lavender-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-lavender-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="macchiato bg-ctp-base grid place-items-center p-3 md:p-12">
            <h2 class="text-2xl font-bold">
                🌺
                <span class="bg-linear-35 from-ctp-red to-ctp-maroon bg-clip-text text-transparent">
                    Macchiato
                </span>
            </h2>
            <table class="mt-3 border-separate border-spacing-1 sm:border-spacing-2">
                <tr class="*:[writing-mode:vertical-lr] sm:*:[writing-mode:horizontal-tb]">
                    <td></td>
                    <td
                        class="text-ctp-subtext0 rotate-180 transform text-left font-mono text-xs font-normal sm:rotate-0 sm:text-center">
                        50</td>
                    <td
                        class="text-ctp-subtext0 rotate-180 transform text-left font-mono text-xs font-normal sm:rotate-0 sm:text-center">
                        100</td>
                    <td
                        class="text-ctp-subtext0 rotate-180 transform text-left font-mono text-xs font-normal sm:rotate-0 sm:text-center">
                        200</td>
                    <td
                        class="text-ctp-subtext0 rotate-180 transform text-left font-mono text-xs font-normal sm:rotate-0 sm:text-center">
                        300</td>
                    <td
                        class="text-ctp-subtext0 rotate-180 transform text-left font-mono text-xs font-normal sm:rotate-0 sm:text-center">
                        400</td>
                    <td
                        class="text-ctp-subtext0 rotate-180 transform text-left font-mono text-xs font-normal sm:rotate-0 sm:text-center">
                        500</td>
                    <td
                        class="text-ctp-subtext0 rotate-180 transform text-left font-mono text-xs font-normal sm:rotate-0 sm:text-center">
                        600</td>
                    <td
                        class="text-ctp-subtext0 rotate-180 transform text-left font-mono text-xs font-normal sm:rotate-0 sm:text-center">
                        700</td>
                    <td
                        class="text-ctp-subtext0 rotate-180 transform text-left font-mono text-xs font-normal sm:rotate-0 sm:text-center">
                        800</td>
                    <td
                        class="text-ctp-subtext0 rotate-180 transform text-left font-mono text-xs font-normal sm:rotate-0 sm:text-center">
                        900</td>
                    <td
                        class="text-ctp-subtext0 rotate-180 transform text-left font-mono text-xs font-normal sm:rotate-0 sm:text-center">
                        950</td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Rosewater</th>
                    <td>
                        <div
                            class="bg-ctp-rosewater-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-rosewater-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-rosewater-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-rosewater-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-rosewater-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-rosewater-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-rosewater-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-rosewater-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-rosewater-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-rosewater-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-rosewater-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Flamingo</th>
                    <td>
                        <div
                            class="bg-ctp-flamingo-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-flamingo-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-flamingo-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-flamingo-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-flamingo-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-flamingo-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-flamingo-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-flamingo-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-flamingo-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-flamingo-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-flamingo-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Pink</th>
                    <td>
                        <div
                            class="bg-ctp-pink-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-pink-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-pink-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-pink-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-pink-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-pink-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-pink-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-pink-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-pink-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-pink-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-pink-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Mauve</th>
                    <td>
                        <div
                            class="bg-ctp-mauve-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-mauve-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-mauve-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-mauve-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-mauve-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-mauve-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-mauve-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-mauve-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-mauve-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-mauve-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-mauve-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Red</th>
                    <td>
                        <div class="bg-ctp-red-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-red-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-red-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-red-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-red-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-red-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-red-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-red-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-red-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-red-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-red-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Maroon</th>
                    <td>
                        <div
                            class="bg-ctp-maroon-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-maroon-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-maroon-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-maroon-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-maroon-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-maroon-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-maroon-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-maroon-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-maroon-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-maroon-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-maroon-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Peach</th>
                    <td>
                        <div
                            class="bg-ctp-peach-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-peach-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-peach-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-peach-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-peach-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-peach-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-peach-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-peach-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-peach-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-peach-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-peach-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Yellow</th>
                    <td>
                        <div
                            class="bg-ctp-yellow-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-yellow-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-yellow-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-yellow-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-yellow-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-yellow-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-yellow-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-yellow-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-yellow-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-yellow-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-yellow-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Green</th>
                    <td>
                        <div
                            class="bg-ctp-green-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-green-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-green-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-green-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-green-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-green-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-green-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-green-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-green-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-green-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-green-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Teal</th>
                    <td>
                        <div
                            class="bg-ctp-teal-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-teal-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-teal-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-teal-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-teal-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-teal-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-teal-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-teal-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-teal-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-teal-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-teal-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Sky</th>
                    <td>
                        <div class="bg-ctp-sky-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sky-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sky-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sky-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sky-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sky-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sky-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sky-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sky-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sky-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sky-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Sapphire</th>
                    <td>
                        <div
                            class="bg-ctp-sapphire-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sapphire-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sapphire-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sapphire-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sapphire-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sapphire-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sapphire-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sapphire-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sapphire-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sapphire-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sapphire-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Blue</th>
                    <td>
                        <div
                            class="bg-ctp-blue-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-blue-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-blue-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-blue-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-blue-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-blue-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-blue-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-blue-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-blue-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-blue-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-blue-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Lavender</th>
                    <td>
                        <div
                            class="bg-ctp-lavender-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-lavender-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-lavender-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-lavender-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-lavender-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-lavender-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-lavender-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-lavender-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-lavender-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-lavender-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-lavender-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="mocha bg-ctp-base flex flex-col items-center p-3 md:p-12">
            <h2 class="text-2xl font-bold">
                🌿
                <span class="bg-linear-35 from-ctp-green to-ctp-teal bg-clip-text text-transparent">
                    Mocha
                </span>
            </h2>
            <table class="mt-3 border-separate border-spacing-1 sm:border-spacing-2">
                <tr class="*:[writing-mode:vertical-lr] sm:*:[writing-mode:horizontal-tb]">
                    <td></td>
                    <td
                        class="text-ctp-subtext0 rotate-180 transform text-left font-mono text-xs font-normal sm:rotate-0 sm:text-center">
                        50</td>
                    <td
                        class="text-ctp-subtext0 rotate-180 transform text-left font-mono text-xs font-normal sm:rotate-0 sm:text-center">
                        100</td>
                    <td
                        class="text-ctp-subtext0 rotate-180 transform text-left font-mono text-xs font-normal sm:rotate-0 sm:text-center">
                        200</td>
                    <td
                        class="text-ctp-subtext0 rotate-180 transform text-left font-mono text-xs font-normal sm:rotate-0 sm:text-center">
                        300</td>
                    <td
                        class="text-ctp-subtext0 rotate-180 transform text-left font-mono text-xs font-normal sm:rotate-0 sm:text-center">
                        400</td>
                    <td
                        class="text-ctp-subtext0 rotate-180 transform text-left font-mono text-xs font-normal sm:rotate-0 sm:text-center">
                        500</td>
                    <td
                        class="text-ctp-subtext0 rotate-180 transform text-left font-mono text-xs font-normal sm:rotate-0 sm:text-center">
                        600</td>
                    <td
                        class="text-ctp-subtext0 rotate-180 transform text-left font-mono text-xs font-normal sm:rotate-0 sm:text-center">
                        700</td>
                    <td
                        class="text-ctp-subtext0 rotate-180 transform text-left font-mono text-xs font-normal sm:rotate-0 sm:text-center">
                        800</td>
                    <td
                        class="text-ctp-subtext0 rotate-180 transform text-left font-mono text-xs font-normal sm:rotate-0 sm:text-center">
                        900</td>
                    <td
                        class="text-ctp-subtext0 rotate-180 transform text-left font-mono text-xs font-normal sm:rotate-0 sm:text-center">
                        950</td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Rosewater</th>
                    <td>
                        <div
                            class="bg-ctp-rosewater-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-rosewater-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-rosewater-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-rosewater-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-rosewater-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-rosewater-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-rosewater-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-rosewater-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-rosewater-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-rosewater-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-rosewater-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Flamingo</th>
                    <td>
                        <div
                            class="bg-ctp-flamingo-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-flamingo-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-flamingo-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-flamingo-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-flamingo-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-flamingo-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-flamingo-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-flamingo-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-flamingo-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-flamingo-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-flamingo-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Pink</th>
                    <td>
                        <div
                            class="bg-ctp-pink-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-pink-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-pink-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-pink-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-pink-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-pink-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-pink-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-pink-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-pink-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-pink-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-pink-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Mauve</th>
                    <td>
                        <div
                            class="bg-ctp-mauve-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-mauve-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-mauve-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-mauve-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-mauve-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-mauve-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-mauve-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-mauve-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-mauve-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-mauve-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-mauve-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Red</th>
                    <td>
                        <div class="bg-ctp-red-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-red-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-red-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-red-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-red-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-red-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-red-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-red-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-red-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-red-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-red-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Maroon</th>
                    <td>
                        <div
                            class="bg-ctp-maroon-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-maroon-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-maroon-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-maroon-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-maroon-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-maroon-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-maroon-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-maroon-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-maroon-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-maroon-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-maroon-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Peach</th>
                    <td>
                        <div
                            class="bg-ctp-peach-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-peach-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-peach-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-peach-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-peach-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-peach-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-peach-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-peach-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-peach-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-peach-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-peach-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Yellow</th>
                    <td>
                        <div
                            class="bg-ctp-yellow-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-yellow-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-yellow-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-yellow-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-yellow-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-yellow-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-yellow-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-yellow-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-yellow-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-yellow-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-yellow-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Green</th>
                    <td>
                        <div
                            class="bg-ctp-green-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-green-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-green-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-green-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-green-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-green-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-green-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-green-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-green-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-green-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-green-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Teal</th>
                    <td>
                        <div
                            class="bg-ctp-teal-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-teal-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-teal-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-teal-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-teal-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-teal-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-teal-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-teal-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-teal-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-teal-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-teal-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Sky</th>
                    <td>
                        <div class="bg-ctp-sky-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sky-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sky-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sky-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sky-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sky-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sky-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sky-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sky-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sky-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sky-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Sapphire</th>
                    <td>
                        <div
                            class="bg-ctp-sapphire-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sapphire-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sapphire-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sapphire-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sapphire-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sapphire-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sapphire-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sapphire-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sapphire-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sapphire-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-sapphire-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Blue</th>
                    <td>
                        <div
                            class="bg-ctp-blue-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-blue-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-blue-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-blue-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-blue-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-blue-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-blue-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-blue-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-blue-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-blue-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-blue-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="text-ctp-subtext0 text-right font-mono text-xs font-normal">Lavender</th>
                    <td>
                        <div
                            class="bg-ctp-lavender-50 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-lavender-100 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-lavender-200 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-lavender-300 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-lavender-400 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-lavender-500 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-lavender-600 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-lavender-700 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-lavender-800 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-lavender-900 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                    <td>
                        <div
                            class="bg-ctp-lavender-950 h-4 w-4 rounded-[2px] sm:h-6 sm:w-6 md:h-10 md:w-10 lg:h-14 lg:w-14">
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <footer class="bg-ctp-mantle text-ctp-subtext0 p-6 text-center text-sm md:p-12">
            Built with <span class="text-ctp-red">&hearts;</span> and
            {{--
              FLUX DEVIATION: flux:link variant="default"
              Text Color: text-ctp-blue (default: theme colors)
              Hover Color: hover:text-ctp-blue-700 (default: subtle underline)
              Underline: no-underline (default: underline)
            --}}
            <flux:link href="https://tailwindcss.com" class="text-ctp-blue hover:text-ctp-blue-700 no-underline">tailwind</flux:link>.
            <br>
            &copy; 2025 <flux:link href="https://catppuccin.com" class="text-ctp-blue hover:text-ctp-blue-700 no-underline">Catppuccin</flux:link>
        </footer>
        @livewireScripts
        @fluxScripts
    </body>

</html>
