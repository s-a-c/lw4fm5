<?php

declare(strict_types=1);

namespace App\Enums;

enum Theme: string
{
    case Default = 'default'; // Default theme with Light/Dark variants
    case AtelierSulphurpool = 'atelier-sulphurpool';
    case Autumn = 'autumn';
    case Ayu = 'ayu';
    case Azure = 'azure';
    case Base16 = 'base16';
    case Berry = 'berry';
    case Catppuccin = 'catppuccin';
    case Citrus = 'citrus';
    case Cobalt2 = 'cobalt2';
    case Coral = 'coral';
    case Cyberpunk = 'cyberpunk';
    case Dracula = 'dracula';
    case Ember = 'ember';
    case Everforest = 'everforest';
    case FinancialTimes = 'financial-times';
    case Forest = 'forest';
    case GitHub = 'github';
    case GovUk = 'gov-uk';
    case Grayscale = 'grayscale';
    case Gruvbox = 'gruvbox';
    case HighContrast = 'high-contrast';
    case Horizon = 'horizon';
    case IceLake = 'ice-lake';
    case Ink = 'ink';
    case Kanagawa = 'kanagawa';
    case Lavender = 'lavender';
    case Lunar = 'lunar';
    case Material = 'material';
    case Matrix = 'matrix';
    case Midnight = 'midnight';
    case Minimal = 'minimal';
    case Monokai = 'monokai';
    case Moss = 'moss';
    case NeonNight = 'neon-night'; // Renamed to group better if preferred, but keeping compliant with css
    case NhsDigital = 'nhs-digital';
    case NightOwl = 'night-owl';
    case Nord = 'nord';
    case Oasis = 'oasis';
    case Oceanic = 'oceanic';
    case OneDarkPro = 'one-dark-pro';
    case OneLight = 'one-light';
    case Palenight = 'palenight';
    case Papercolor = 'papercolor';
    case Pastel = 'pastel';
    case Pearl = 'pearl';
    case Plasma = 'plasma';
    case Poimandres = 'poimandres';
    case Retro = 'retro';
    case RoseGold = 'rose-gold';
    case RosePine = 'rose-pine';
    case Sandstorm = 'sandstorm';
    case Solarized = 'solarized';
    case Sunset = 'sunset';
    case Synthwave = 'synthwave';
    case Teal = 'teal';
    case TheGuardian = 'the-guardian';
    case TokyoNight = 'tokyo-night';
    case TransportForLondon = 'transport-for-london';
    case VioletNight = 'violet-night';

    public function label(): string
    {
        return match ($this) {
            self::Default => 'Default',
            self::AtelierSulphurpool => 'Atelier Sulphurpool',
            self::Autumn => 'Autumn',
            self::Ayu => 'Ayu',
            self::Azure => 'Azure',
            self::Base16 => 'Base16',
            self::Berry => 'Berry',
            self::Catppuccin => 'Catppuccin',
            self::Citrus => 'Citrus',
            self::Cobalt2 => 'Cobalt2',
            self::Coral => 'Coral',
            self::Cyberpunk => 'Cyberpunk',
            self::Dracula => 'Dracula',
            self::Ember => 'Ember',
            self::Everforest => 'Everforest',
            self::FinancialTimes => 'Financial Times',
            self::Forest => 'Forest',
            self::GitHub => 'GitHub',
            self::GovUk => 'GOV.UK',
            self::Grayscale => 'Grayscale',
            self::Gruvbox => 'Gruvbox',
            self::HighContrast => 'High Contrast',
            self::Horizon => 'Horizon',
            self::IceLake => 'Ice Lake',
            self::Ink => 'Ink',
            self::Kanagawa => 'Kanagawa',
            self::Lavender => 'Lavender',
            self::Lunar => 'Lunar',
            self::Material => 'Material',
            self::Matrix => 'Matrix',
            self::Midnight => 'Midnight',
            self::Minimal => 'Minimal',
            self::Monokai => 'Monokai',
            self::Moss => 'Moss',
            self::NeonNight => 'Neon Night',
            self::NhsDigital => 'NHS Digital',
            self::NightOwl => 'Night Owl',
            self::Nord => 'Nord',
            self::Oasis => 'Oasis',
            self::Oceanic => 'Oceanic Next',
            self::OneDarkPro => 'One Dark Pro',
            self::OneLight => 'One Light',
            self::Palenight => 'Palenight',
            self::Papercolor => 'Papercolor',
            self::Pastel => 'Pastel',
            self::Pearl => 'Pearl',
            self::Plasma => 'Plasma',
            self::Poimandres => 'Poimandres',
            self::Retro => 'Retro',
            self::RoseGold => 'Rose Gold',
            self::RosePine => 'Rose Pine',
            self::Sandstorm => 'Sandstorm',
            self::Solarized => 'Solarized',
            self::Sunset => 'Sunset',
            self::Synthwave => 'Synthwave',
            self::Teal => 'Teal',
            self::TheGuardian => 'The Guardian',
            self::TokyoNight => 'Tokyo Night',
            self::TransportForLondon => 'Transport for London',
            self::VioletNight => 'Violet Night',
        };
    }

    /**
     * @return array<int, ThemeFlavor>
     */
    public function flavors(): array
    {
        return match ($this) {
            self::Default => [ThemeFlavor::Light, ThemeFlavor::Dark, ThemeFlavor::System],

            // Multi-flavor themes
            self::Ayu => [ThemeFlavor::Dark, ThemeFlavor::Mirage, ThemeFlavor::Light],
            self::Base16 => [ThemeFlavor::Dark, ThemeFlavor::Light],
            self::Catppuccin => [ThemeFlavor::Latte, ThemeFlavor::Frappe, ThemeFlavor::Macchiato, ThemeFlavor::Mocha],
            self::Everforest => [ThemeFlavor::Dark, ThemeFlavor::Light],
            self::GitHub => [ThemeFlavor::Dark, ThemeFlavor::Light],
            self::Grayscale => [ThemeFlavor::Dark, ThemeFlavor::Light],
            self::Gruvbox => [ThemeFlavor::Dark, ThemeFlavor::Light],
            self::HighContrast => [ThemeFlavor::Dark, ThemeFlavor::Light],
            self::Kanagawa => [ThemeFlavor::Wave, ThemeFlavor::Dragon, ThemeFlavor::Lotus],
            self::Material => [ThemeFlavor::Dark, ThemeFlavor::Light],
            self::Papercolor => [ThemeFlavor::Dark, ThemeFlavor::Light],
            self::RosePine => [ThemeFlavor::Dark, ThemeFlavor::Moon, ThemeFlavor::Dawn],
            self::Solarized => [ThemeFlavor::Dark, ThemeFlavor::Light],
            self::TokyoNight => [ThemeFlavor::Night, ThemeFlavor::Storm, ThemeFlavor::Moon],

            // Specific named flavors
            self::Cyberpunk => [ThemeFlavor::Neon],
            self::Monokai => [ThemeFlavor::Classic], // Maps to 'classic' or standard
            self::Oceanic => [ThemeFlavor::Next],
            self::Synthwave => [ThemeFlavor::EightyFour], // '84'

            // Single flavor (Dark)
            self::AtelierSulphurpool, self::Autumn, self::Azure, self::Berry,
            self::Citrus, self::Cobalt2, self::Coral, self::Dracula,
            self::Ember, self::Forest, self::Horizon, self::IceLake,
            self::Ink, self::Lavender, self::Lunar, self::Matrix,
            self::Midnight, self::Moss, self::NeonNight, self::NightOwl,
            self::Nord, self::Oasis, self::OneDarkPro, self::Palenight,
            self::Plasma, self::Poimandres, self::RoseGold, self::Sandstorm,
            self::Sunset, self::Teal, self::VioletNight => [ThemeFlavor::Dark],

            // Single flavor (Light/Other)
            self::GovUk, self::FinancialTimes, self::Minimal, self::NhsDigital,
            self::OneLight, self::Pastel, self::Pearl, self::Retro,
            self::TheGuardian, self::TransportForLondon => [ThemeFlavor::Light],
        };
    }
}
