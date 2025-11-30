<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\Theme;
use App\Enums\ThemeAccent;
use App\Enums\ThemeFlavor;
use Spatie\LaravelData\Data;

final class ThemeData extends Data
{
    public function __construct(
        public Theme $theme,
        public ThemeFlavor $flavor,
        public ThemeAccent $accent,
    ) {}

    public function isLight(): bool
    {
        // Default theme with System flavor - check OS/browser preference
        if ($this->theme === Theme::Default && $this->flavor === ThemeFlavor::System) {
            // Check prefers-color-scheme media query via JavaScript
            // For server-side rendering, default to false (dark) but JavaScript will override
            return false;
        }

        // Default theme with explicit Light/Dark flavor
        if ($this->theme === Theme::Default) {
            return $this->flavor === ThemeFlavor::Light;
        }

        // Light-only themes (single-flavor themes that are always light)
        $lightOnlyThemes = [
            Theme::GovUk,
            Theme::TransportForLondon,
            Theme::NhsDigital,
            Theme::FinancialTimes,
            Theme::TheGuardian,
        ];

        if (in_array($this->theme, $lightOnlyThemes, true)) {
            return true;
        }

        return $this->flavor->isLight();
    }
}
