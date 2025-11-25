<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\Theme;
use App\Enums\ThemeAccent;
use App\Enums\ThemeFlavor;
use Spatie\LaravelData\Data;

final class UserSettingsData extends Data
{
    public function __construct(
        public Theme $theme = Theme::Catppuccin,
        public ThemeFlavor $flavor = ThemeFlavor::Mocha,
        public ThemeAccent $accent = ThemeAccent::Primary,
    ) {}
}
