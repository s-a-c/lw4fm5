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
        // None theme (system default) - let OS/browser preference control
        if ($this->theme === Theme::None) {
            return false; // Default to dark, but OS preference will override
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
