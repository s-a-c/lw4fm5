@php
    use App\Data\ThemeData;
    use App\Enums\Theme;
    use App\Enums\ThemeAccent;
    use App\Enums\ThemeFlavor;

    /** @var ThemeData $resolvedThemeData */
    $resolvedThemeData = $themeData
        ?? new ThemeData(
            theme: Theme::Catppuccin,
            flavor: ThemeFlavor::Mocha,
            accent: ThemeAccent::Primary,
        );

           $isDefaultTheme = ($resolvedThemeData->theme === Theme::Default) || ($resolvedThemeData->theme->value === 'default');
           $themeJson = json_encode($resolvedThemeData->theme->value, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
           // Default theme has Light, Dark, System flavors - use the actual flavor value
           $flavorJson = json_encode($resolvedThemeData->flavor->value, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
           // Default theme uses accents like other themes
           $accentJson = json_encode($resolvedThemeData->accent->value, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
    $isLightJson = json_encode($resolvedThemeData->isLight(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
@endphp
<script @cspNonce>
    (function () {
        'use strict';

        if (typeof document === 'undefined' || !document.documentElement) {
            return;
        }

        try {
            const r = document.documentElement;
            const themeValue = {!! $themeJson !!};
            const flavorValue = {!! $flavorJson !!};
            const accentValue = {!! $accentJson !!};
            const isLightValue = {!! $isLightJson !!};

            // Set data attributes
            r.dataset.theme = themeValue;
            r.dataset.flavor = flavorValue;
            r.dataset.accent = accentValue;

            // Determine dark mode based on theme settings
            let shouldBeDark = false;
            let useSystemPreference = false;

            // Handle 'default' theme with System flavor - check OS preference
            if (themeValue === 'default' && flavorValue === 'system') {
                // For system flavor, let OS/browser preference control dark mode
                const prefersDark = window.matchMedia?.('(prefers-color-scheme: dark)').matches ?? false;
                shouldBeDark = prefersDark;
                useSystemPreference = true;
            } else if (themeValue === 'default') {
                // For explicit Light/Dark flavors on Default theme
                shouldBeDark = flavorValue === 'dark';
            } else if (themeValue !== null) {
                // For other themes, use isLight value
                shouldBeDark = !isLightValue;
            }

            // Apply dark mode class immediately
            r.classList.toggle('dark', shouldBeDark);

            // Sync with Flux UI localStorage to prevent Flux from overriding our theme
            try {
                if (typeof localStorage !== 'undefined') {
                    if (useSystemPreference) {
                        // For system flavor, let Flux use system preference
                        localStorage.removeItem('flux.appearance');
                    } else {
                        // For explicit themes, set Flux appearance to match
                        localStorage.setItem('flux.appearance', shouldBeDark ? 'dark' : 'light');
                    }
                }
            } catch (storageErr) {
                // localStorage may not be available in some contexts
            }

            // Store theme state globally so we can re-apply after Flux runs
            window.__themeState = {
                theme: themeValue,
                flavor: flavorValue,
                accent: accentValue,
                isLight: isLightValue,
                shouldBeDark: shouldBeDark,
                useSystemPreference: useSystemPreference
            };

            // Re-apply dark mode class after a microtask to override Flux
            // This ensures our theme settings take precedence over Flux's applyAppearance
            queueMicrotask(function() {
                const state = window.__themeState;
                if (state && !state.useSystemPreference) {
                    document.documentElement.classList.toggle('dark', state.shouldBeDark);
                }
            });

            // Also re-apply after DOMContentLoaded in case Flux runs later
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    const state = window.__themeState;
                    if (state && !state.useSystemPreference) {
                        document.documentElement.classList.toggle('dark', state.shouldBeDark);
                    }
                }, { once: true });
            }

        } catch (e) {
            try {
                const r = document.documentElement;
                if (!r.dataset.theme) r.dataset.theme = 'catppuccin';
                if (!r.dataset.flavor) r.dataset.flavor = 'mocha';
                if (!r.dataset.accent) r.dataset.accent = 'primary';
            } catch (inner) {
                // ignore
            }
        }
    })();
</script>
