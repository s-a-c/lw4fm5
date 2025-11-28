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

    $isNoneTheme = ($resolvedThemeData->theme === Theme::None) || ($resolvedThemeData->theme->value === 'none');
    $themeJson = json_encode($resolvedThemeData->theme->value, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
    $flavorJson = json_encode($isNoneTheme ? 'none' : $resolvedThemeData->flavor->value, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
    $accentJson = json_encode($isNoneTheme ? 'none' : $resolvedThemeData->accent->value, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
    $isLightJson = json_encode($resolvedThemeData->isLight(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
@endphp
<script>
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

            // Set data attributes (including 'none' for system default)
            r.dataset.theme = themeValue;
            r.dataset.flavor = flavorValue;
            r.dataset.accent = accentValue;

            // For 'none' theme, let OS/browser preference control dark mode
            // Don't force dark class - let prefers-color-scheme handle it
            if (themeValue !== 'none' && themeValue !== null) {

                if (isLightValue) {
                    r.classList.remove('dark');
                } else {
                    r.classList.add('dark');
                }
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
