<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Enums\Theme;
use App\Enums\ThemeAccent;
use App\Enums\ThemeFlavor;

interface ThemeAccentMapperInterface
{
    /**
     * @return array<int, ThemeAccent>
     */
    public function getAvailableAccents(Theme $theme): array;

    public function validateAccent(Theme $theme, ThemeAccent $accent): bool;

    public function getFluxVariableName(Theme $theme, ThemeFlavor $flavor, ThemeAccent $accent): string;

    public function getFilamentVariableName(Theme $theme, ThemeFlavor $flavor, ThemeAccent $accent): string;
}
