<?php

declare(strict_types=1);

namespace App\Providers;

use FilesystemIterator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Livewire\LivewireManager;
use Livewire\Volt\Component;
use Livewire\Volt\ComponentResolver;
use Livewire\Volt\MountedDirectories;
use Livewire\Volt\MountedDirectory;
use Livewire\Volt\Volt;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Throwable;

final class VoltServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Volt::mount([
            config('livewire.view_path', resource_path('views/livewire')),
            resource_path('views/pages'),
        ]);

        $this->registerVoltComponentAliases();
    }

    /**
     * Ensure Volt components can resolve their registered aliases when rendered.
     */
    private function registerVoltComponentAliases(): void
    {
        $mountedDirectories = app(MountedDirectories::class);
        $directories = collect($mountedDirectories->paths());

        if ($directories->isEmpty()) {
            return;
        }

        $livewireManager = app(LivewireManager::class);
        $componentResolver = app(ComponentResolver::class);

        /** @var array<int, string> $allMountPaths */
        $allMountPaths = $directories
            ->map(static fn (MountedDirectory $directory): string => $directory->path)
            ->values()
            ->all();

        foreach ($directories as $directory) {
            foreach ($this->discoverVoltComponentAliases($directory) as $alias) {
                $class = $this->resolveComponentClass($componentResolver, $alias, $allMountPaths);

                if ($class === null) {
                    continue;
                }

                $livewireManager->component($alias, $class);
            }
        }
    }

    /**
     * Resolve component class name from alias, returning null if resolution fails.
     *
     * @param  array<int, string>  $allMountPaths
     * @return string|null The resolved class name, or null if resolution fails
     */
    private function resolveComponentClass(
        ComponentResolver $componentResolver,
        string $alias,
        array $allMountPaths
    ): ?string {
        $class = $this->attemptComponentResolution($componentResolver, $alias, $allMountPaths);

        if ($class === null) {
            return null;
        }

        if ($this->isValidClassString($class)) {
            /** @var string $classString */
            $classString = $class;
            if ($this->classExists($classString)) {
                return $classString;
            }
        }

        return null;
    }

    /**
     * Attempt to resolve component class from resolver, returning null on failure.
     *
     * @param  array<int, string>  $allMountPaths
     * @return mixed The resolved class name or null if resolution fails
     */
    private function attemptComponentResolution(
        ComponentResolver $componentResolver,
        string $alias,
        array $allMountPaths
    ): mixed {
        try {
            return $componentResolver->resolve($alias, $allMountPaths);
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * Check if the resolved value is a valid string class name.
     */
    private function isValidClassString(mixed $class): bool
    {
        return is_string($class);
    }

    /**
     * Check if the class exists.
     */
    private function classExists(string $class): bool
    {
        return class_exists($class);
    }

    /**
     * @return array<int, string>
     */
    private function discoverVoltComponentAliases(MountedDirectory $directory): array
    {
        if (! is_dir($directory->path)) {
            return [];
        }

        $aliases = [];

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory->path, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            if (! $file->isFile()) {
                continue;
            }

            $filename = $file->getFilename();
            if (! Str::endsWith($filename, '.blade.php')) {
                continue;
            }

            $contents = @file_get_contents($file->getPathname());
            if ($contents === false) {
                continue;
            }
            if (! Str::contains($contents, [Component::class, '@volt'])) {
                continue;
            }

            $relativePath = Str::after($file->getPathname(), $directory->path.DIRECTORY_SEPARATOR);
            $relativePath = Str::replaceLast('.blade.php', '', $relativePath);
            $alias = str_replace(['/', '\\'], '.', $relativePath);
            $alias = mb_trim($alias, '.');

            if ($alias === '') {
                continue;
            }

            $aliases[] = $alias;
        }

        sort($aliases);

        return array_values(array_unique($aliases));
    }
}
