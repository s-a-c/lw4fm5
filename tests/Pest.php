<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Sleep;
use Illuminate\Support\Str;
use Tests\TestCase;

$pestPluginRegistry = dirname(__DIR__).DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'pest-plugins.json';

if (is_file($pestPluginRegistry)) {
    $contents = file_get_contents($pestPluginRegistry);

    if ($contents !== false) {
        try {
            $plugins = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            $plugins = null;
        }

        if (is_array($plugins)) {
            $mutatePlugin = 'Pest\\Mutate\\Plugins\\Mutate';

            $filteredPlugins = array_values(array_filter(
                $plugins,
                static function (string $plugin) use ($mutatePlugin): bool {
                    return $plugin !== $mutatePlugin;
                },
            ));

            if ($filteredPlugins !== $plugins) {
                try {
                    $encoded = json_encode($filteredPlugins, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
                } catch (JsonException) {
                    $encoded = null;
                }

                if ($encoded !== null) {
                    file_put_contents($pestPluginRegistry, $encoded);
                }
            }
        }
    }
}

/*
|--------------------------------------------------------------------------
| Test Case & Defaults
|--------------------------------------------------------------------------
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->beforeEach(function (): void {
        Str::createRandomStringsNormally();
        Str::createUuidsNormally();
        Http::preventStrayRequests();
        Process::preventStrayProcesses();
        Sleep::fake();

        $this->freezeTime();
    })
    ->in('Architecture', 'Browser', 'Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
*/

expect()->extend('toBeOne', fn () => $this->toBe(1));

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
*/

function something(): void
{
    // ..
}
