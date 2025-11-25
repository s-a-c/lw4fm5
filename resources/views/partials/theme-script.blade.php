@php
    // Always define defaults first to prevent any undefined variable errors
    $theme = 'catppuccin';
    $flavor = 'mocha';
    $accent = 'primary';
    $isLight = false;

    // Override with shared values if they exist
    if (isset($themeValue)) {
        $theme = $themeValue;
    }
    if (isset($flavorValue)) {
        $flavor = $flavorValue;
    }
    if (isset($accentValue)) {
        $accent = $accentValue;
    }
    if (isset($isLightValue)) {
        $isLight = (bool) $isLightValue;
    }

    $themeJson = json_encode($theme, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
    $flavorJson = json_encode($flavor, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
    $accentJson = json_encode($accent, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
    $isLightJson = json_encode($isLight, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
@endphp
<script>
    (function() {
        'use strict';
        if (typeof document === "undefined" || !document.documentElement) {
            return;
        }
        try {
            const r = document.documentElement;
            const themeValue = {!! $themeJson !!};
            const flavorValue = {!! $flavorJson !!};
            const accentValue = {!! $accentJson !!};
            const isLightValue = {!! $isLightJson !!};

            r.dataset.theme = themeValue;
            r.dataset.flavor = flavorValue;
            r.dataset.accent = accentValue;

            // Handle Dark Mode toggling based on Flavor
            if (isLightValue) {
                r.classList.remove("dark");
            } else {
                r.classList.add("dark");
            }
        } catch (e) {
            // Silently fail in test environments - ensure defaults are set
            try {
                const r = document.documentElement;
                if (!r.dataset.theme) r.dataset.theme = 'catppuccin';
                if (!r.dataset.flavor) r.dataset.flavor = 'mocha';
                if (!r.dataset.accent) r.dataset.accent = 'primary';
            } catch (e2) {
                // Ignore
            }
        }
    })();
</script>
