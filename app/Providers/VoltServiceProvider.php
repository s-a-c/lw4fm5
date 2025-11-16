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
        //
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

        $allMountPaths = $directories
            ->map(static fn (MountedDirectory $directory): string => $directory->path)
            ->all();

        foreach ($directories as $directory) {
            foreach ($this->discoverVoltComponentAliases($directory) as $alias) {
                try {
                    $class = $componentResolver->resolve($alias, $allMountPaths);
                } catch (Throwable) {
                    continue;
                }
                if (! is_string($class)) {
                    continue;
                }
                if (! class_exists($class)) {
                    continue;
                }

                $livewireManager->component($alias, $class);
            }
        }
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
            new RecursiveDirectoryIterator($directory->path, FilesystemIterator::SKIP_DOTS)
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
