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
            $this->onFilamentServing();
        });

        View::composer('filament::assets', function (): void {
            $this->configureFilamentAssets();
        });
    }

    /**
     * Handle Filament serving event.
     * This method is called from the Filament::serving callback.
     */
    private function onFilamentServing(): void
    {
        $this->configureFilamentAssets();
    }

    private function configureFilamentAssets(): void
    {
        $scriptConfigRaw = config('filament.assets.scripts', []);
        $scriptConfig = is_array($scriptConfigRaw) ? $scriptConfigRaw : [];

        $defer = isset($scriptConfig['defer']) && is_bool($scriptConfig['defer']) && $scriptConfig['defer'];
        $async = isset($scriptConfig['async']) && is_bool($scriptConfig['async']) && $scriptConfig['async'];

        $attributesRaw = $scriptConfig['attributes'] ?? [];
        $attributesArray = is_array($attributesRaw) ? $attributesRaw : [];
        /** @var array<string, bool|int|string> $attributesFiltered */
        $attributesFiltered = array_filter($attributesArray, fn (mixed $v): bool => is_bool($v) || is_int($v) || is_string($v));
        $extraAttributes = $this->stringifyAttributes($attributesFiltered);

        $targetsRaw = $scriptConfig['targets'] ?? ['*'];
        $targetsArray = is_array($targetsRaw) ? $targetsRaw : ['*'];
        /** @var array<int, string> $targetsFiltered */
        $targetsFiltered = array_values(array_filter($targetsArray, is_string(...)));
        $targets = $this->normalizeIdentifiers($targetsFiltered);

        $excludesRaw = $scriptConfig['exclude'] ?? [];
        $excludesArray = is_array($excludesRaw) ? $excludesRaw : [];
        /** @var array<int, string> $excludesFiltered */
        $excludesFiltered = array_values(array_filter($excludesArray, is_string(...)));
        $excludes = $this->normalizeIdentifiers($excludesFiltered);
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
                $existingAttributes = $script->getExtraAttributes();
                $extraArray = $extraAttributes->all();
                /** @var array<string, string> $combined */
                $combined = array_merge($existingAttributes, $extraArray);
                $script->extraAttributes($combined);
            }
        }

        if (! $loadAlpine) {
            foreach (FilamentAsset::getAlpineComponents() as $component) {
                $component->loadedOnRequest();
            }
        }
    }

    /**
     * @param  array<string, bool|int|string>  $attributes
     * @return Collection<string, non-empty-string>
     */
    private function stringifyAttributes(array $attributes): Collection
    {
        return collect($attributes)
            ->mapWithKeys(static fn (string|int|bool $value, string $key): array => [$key => (string) $value])
            ->reject(static fn (string $value): bool => $value === '');
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
