<?php

declare(strict_types=1);

namespace App\Enums;

enum Theme: string
{
    case None = 'none'; // System default - no theme applied
    case Catppuccin = 'catppuccin';
    case Kanagawa = 'kanagawa';
    case TokyoNight = 'tokyo-night';
    case Dracula = 'dracula';
    case Nord = 'nord';
    case RosePine = 'rose-pine';
    case OneDarkPro = 'one-dark-pro';
    case MonokaiPro = 'monokai-pro';
    case Gruvbox = 'gruvbox';
    case Solarized = 'solarized';
    case GovUk = 'gov-uk';
    case TransportForLondon = 'transport-for-london';
    case NhsDigital = 'nhs-digital';
    case FinancialTimes = 'financial-times';
    case TheGuardian = 'the-guardian';

    public function label(): string
    {
        return match ($this) {
            self::None => 'None (System Default)',
            self::Catppuccin => 'Catppuccin',
            self::Kanagawa => 'Kanagawa',
            self::TokyoNight => 'Tokyo Night',
            self::Dracula => 'Dracula',
            self::Nord => 'Nord',
            self::RosePine => 'Rose Pine',
            self::OneDarkPro => 'One Dark Pro',
            self::MonokaiPro => 'Monokai Pro',
            self::Gruvbox => 'Gruvbox',
            self::Solarized => 'Solarized',
            self::GovUk => 'GOV.UK',
            self::TransportForLondon => 'Transport for London',
            self::NhsDigital => 'NHS Digital',
            self::FinancialTimes => 'Financial Times',
            self::TheGuardian => 'The Guardian',
        };
    }

    /**
     * @return array<int, ThemeFlavor>
     */
    public function flavors(): array
    {
        return match ($this) {
            self::None => [], // No flavors for system default
            self::Catppuccin => [
                ThemeFlavor::Latte,
                ThemeFlavor::Frappe,
                ThemeFlavor::Macchiato,
                ThemeFlavor::Mocha,
            ],
            self::Kanagawa => [
                ThemeFlavor::Wave,
                ThemeFlavor::Dragon,
                ThemeFlavor::Lotus,
            ],
            self::TokyoNight => [
                ThemeFlavor::Night,
                ThemeFlavor::Day,
            ],
            self::Gruvbox => [
                ThemeFlavor::Dark,
                ThemeFlavor::Light,
            ],
            self::Solarized => [
                ThemeFlavor::Dark,
                ThemeFlavor::Light,
            ],
            // Single-flavor themes
            self::Dracula => [
                ThemeFlavor::Default,
            ],
            self::Nord => [
                ThemeFlavor::Default,
            ],
            self::RosePine => [
                ThemeFlavor::Default,
            ],
            self::OneDarkPro => [
                ThemeFlavor::Default,
            ],
            self::MonokaiPro => [
                ThemeFlavor::Default,
            ],
            self::GovUk => [
                ThemeFlavor::Default,
            ],
            self::TransportForLondon => [
                ThemeFlavor::Default,
            ],
            self::NhsDigital => [
                ThemeFlavor::Default,
            ],
            self::FinancialTimes => [
                ThemeFlavor::Default,
            ],
            self::TheGuardian => [
                ThemeFlavor::Default,
            ],
        };
    }
}
