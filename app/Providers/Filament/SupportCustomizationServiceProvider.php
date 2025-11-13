<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use Filament\Facades\Filament;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

/**
 * Compliant with [AI-GUIDELINES.md](../../.ai/AI-GUIDELINES.md) v0921d4cfab198af1451ef177b6e47657b5d3ab0292f77bf232496291dee47183
 */
final class SupportCustomizationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../../config/filament/assets.php',
            'filament.assets',
        );
    }

    public function boot(): void
    {
        Filament::serving(function (): void {
            $this->configureFilamentAssets();
        });

        View::composer('filament::assets', function (): void {
            $this->configureFilamentAssets();
        });
    }

    private function configureFilamentAssets(): void
    {
        $scriptConfig = config('filament.assets.scripts', []);
        $defer = (bool) ($scriptConfig['defer'] ?? false);
        $async = (bool) ($scriptConfig['async'] ?? false);
        $extraAttributes = $this->stringifyAttributes(
            (array) ($scriptConfig['attributes'] ?? []),
        );
        $targets = $this->normalizeIdentifiers(
            (array) ($scriptConfig['targets'] ?? ['*']),
        );
        $excludes = $this->normalizeIdentifiers(
            (array) ($scriptConfig['exclude'] ?? []),
        );
        $loadAlpine = (bool) config('filament.assets.load_alpine', true);

        foreach (FilamentAsset::getScripts() as $script) {
            if (! $this->shouldMutateScript($script, $targets)) {
                continue;
            }

            if ($this->shouldExcludeScript($script, $excludes)) {
                $script->loadedOnRequest();

                continue;
            }

            if ($defer) {
                $script->defer();
            }

            if ($async) {
                $script->async();
            }

            if ($extraAttributes->isNotEmpty()) {
                $script->extraAttributes([
                    ...$script->getExtraAttributes(),
                    ...$extraAttributes->all(),
                ]);
            }
        }

        if (! $loadAlpine) {
            foreach (FilamentAsset::getAlpineComponents() as $component) {
                $component->loadedOnRequest();
            }
        }
    }

    /**
     * @param  array<string, string|int|bool>  $attributes
     */
    private function stringifyAttributes(array $attributes): Collection
    {
        return collect($attributes)
            ->mapWithKeys(static fn (string|int|bool $value, string $key): array => [$key => (string) $value])
            ->filter();
    }

    /**
     * @param  array<int, string>  $identifiers
     * @return array<int, string>
     */
    private function normalizeIdentifiers(array $identifiers): array
    {
        return collect($identifiers)
            ->map(static fn (string $identifier): string => mb_trim($identifier))
            ->filter()
            ->map(static fn (string $identifier): string => mb_strtolower($identifier))
            ->values()
            ->all();
    }

    /**
     * @param  array<int, string>  $targets
     */
    private function shouldMutateScript(Js $script, array $targets): bool
    {
        if (in_array('*', $targets, true)) {
            return true;
        }

        $identifier = $this->scriptIdentifier($script);

        return in_array($identifier, $targets, true);
    }

    /**
     * @param  array<int, string>  $excludes
     */
    private function shouldExcludeScript(Js $script, array $excludes): bool
    {
        if ($excludes === []) {
            return false;
        }

        $identifier = $this->scriptIdentifier($script);

        return in_array($identifier, $excludes, true);
    }

    private function scriptIdentifier(Js $script): string
    {
        $package = $script->getPackage() ?? 'app';

        return mb_strtolower(sprintf('%s:%s', $package, $script->getId()));
    }
}
