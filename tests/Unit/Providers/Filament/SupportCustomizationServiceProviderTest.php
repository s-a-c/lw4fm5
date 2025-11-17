<?php

declare(strict_types=1);

use App\Providers\Filament\SupportCustomizationServiceProvider;
use Filament\Facades\Filament;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Mockery as m;

it('boots SupportCustomizationServiceProvider', function (): void {
    expect(App::getProvider(SupportCustomizationServiceProvider::class))->not->toBeNull();
});

it('configures Filament assets on serving event', function (): void {
    Config::set('filament.assets.scripts', [
        'targets' => ['*'],
        'defer' => true,
        'async' => false,
    ]);

    // Mock FilamentAsset to return scripts
    $script = m::mock(Js::class);
    $script->shouldReceive('getPackage')->andReturn('filament');
    $script->shouldReceive('getId')->andReturn('app');
    $script->shouldReceive('defer')->once();
    $script->shouldReceive('getExtraAttributes')->andReturn([]);

    FilamentAsset::shouldReceive('getScripts')->andReturn([$script]);
    FilamentAsset::shouldReceive('getAlpineComponents')->andReturn([]);

    // Boot the provider to register the serving callback (line 30)
    $provider = new SupportCustomizationServiceProvider(App::getInstance());
    $provider->boot();

    // Use reflection to call configureFilamentAssets directly to test line 30 execution
    $reflection = new ReflectionClass($provider);
    $method = $reflection->getMethod('configureFilamentAssets');
    $method->setAccessible(true);
    $method->invoke($provider);

    expect(App::getProvider(SupportCustomizationServiceProvider::class))->not->toBeNull();
});

it('configures Filament assets on view composer', function (): void {
    Config::set('filament.assets.scripts', [
        'targets' => ['filament:app'], // scriptIdentifier returns lowercase "package:id"
        'defer' => false,
        'async' => true,
    ]);

    // Mock FilamentAsset to return scripts
    $script = m::mock(Js::class);
    $script->shouldReceive('getPackage')->andReturn('filament');
    $script->shouldReceive('getId')->andReturn('app');
    $script->shouldReceive('async')->once();
    $script->shouldReceive('getExtraAttributes')->andReturn([]);

    FilamentAsset::shouldReceive('getScripts')->andReturn([$script]);
    FilamentAsset::shouldReceive('getAlpineComponents')->andReturn([]);

    // Boot provider
    $provider = new SupportCustomizationServiceProvider(App::getInstance());
    $provider->boot();

    // Use reflection to call configureFilamentAssets directly
    $reflection = new ReflectionClass($provider);
    $method = $reflection->getMethod('configureFilamentAssets');
    $method->setAccessible(true);
    $method->invoke($provider);

    expect(true)->toBeTrue();
});

it('skips scripts that should not be mutated', function (): void {
    Config::set('filament.assets.scripts', [
        'targets' => ['filament/app'], // Specific target, not '*'
        'defer' => true,
    ]);

    // Mock a script that doesn't match the target (line 67 continue)
    $script = m::mock(Js::class);
    $script->shouldReceive('getPackage')->andReturn('other-package');
    $script->shouldReceive('getId')->andReturn('other-script');
    // Should not receive defer() call since it doesn't match target

    FilamentAsset::shouldReceive('getScripts')->andReturn([$script]);
    FilamentAsset::shouldReceive('getAlpineComponents')->andReturn([]);

    $provider = new SupportCustomizationServiceProvider(App::getInstance());
    $provider->boot();

    // Use reflection to call configureFilamentAssets directly
    $reflection = new ReflectionClass($provider);
    $method = $reflection->getMethod('configureFilamentAssets');
    $method->setAccessible(true);
    $method->invoke($provider);

    expect(true)->toBeTrue();
});

it('applies async attribute to scripts', function (): void {
    Config::set('filament.assets.scripts', [
        'targets' => ['*'],
        'async' => true,
    ]);

    // Mock script to test line 81 (async call)
    $script = m::mock(Js::class);
    $script->shouldReceive('getPackage')->andReturn('filament');
    $script->shouldReceive('getId')->andReturn('app');
    $script->shouldReceive('async')->once(); // Line 81
    $script->shouldReceive('getExtraAttributes')->andReturn([]);

    FilamentAsset::shouldReceive('getScripts')->andReturn([$script]);
    FilamentAsset::shouldReceive('getAlpineComponents')->andReturn([]);

    $provider = new SupportCustomizationServiceProvider(App::getInstance());
    $provider->boot();

    // Use reflection to call configureFilamentAssets directly
    $reflection = new ReflectionClass($provider);
    $method = $reflection->getMethod('configureFilamentAssets');
    $method->setAccessible(true);
    $method->invoke($provider);

    expect(App::getProvider(SupportCustomizationServiceProvider::class))->not->toBeNull();
});

it('mutates scripts based on identifier match', function (): void {
    Config::set('filament.assets.scripts', [
        'targets' => ['filament:app'], // scriptIdentifier returns lowercase "package:id"
        'defer' => true,
    ]);

    // Mock script that matches the identifier (lines 138-140)
    $script = m::mock(Js::class);
    $script->shouldReceive('getPackage')->andReturn('filament');
    $script->shouldReceive('getId')->andReturn('app');
    $script->shouldReceive('defer')->once();
    $script->shouldReceive('getExtraAttributes')->andReturn([]);

    FilamentAsset::shouldReceive('getScripts')->andReturn([$script]);
    FilamentAsset::shouldReceive('getAlpineComponents')->andReturn([]);

    $provider = new SupportCustomizationServiceProvider(App::getInstance());
    $provider->boot();

    // Use reflection to call configureFilamentAssets directly
    $reflection = new ReflectionClass($provider);
    $method = $reflection->getMethod('configureFilamentAssets');
    $method->setAccessible(true);
    $method->invoke($provider);

    expect(App::getProvider(SupportCustomizationServiceProvider::class))->not->toBeNull();
});

it('excludes scripts that match exclude list', function (): void {
    Config::set('filament.assets.scripts', [
        'targets' => ['*'],
        'exclude' => ['filament:app'], // scriptIdentifier returns lowercase "package:id"
    ]);

    // Mock script that matches exclude (line 71: loadedOnRequest)
    $script = m::mock(Js::class);
    $script->shouldReceive('getPackage')->andReturn('filament');
    $script->shouldReceive('getId')->andReturn('app');
    $script->shouldReceive('loadedOnRequest')->once(); // Line 71

    FilamentAsset::shouldReceive('getScripts')->andReturn([$script]);
    FilamentAsset::shouldReceive('getAlpineComponents')->andReturn([]);

    $provider = new SupportCustomizationServiceProvider(App::getInstance());
    $provider->boot();

    $reflection = new ReflectionClass($provider);
    $method = $reflection->getMethod('configureFilamentAssets');
    $method->setAccessible(true);
    $method->invoke($provider);

    expect(true)->toBeTrue();
});

it('applies extra attributes to scripts', function (): void {
    Config::set('filament.assets.scripts', [
        'targets' => ['*'],
        'attributes' => [
            'data-test' => 'value',
            'data-qa' => 'true',
        ],
    ]);

    // Mock script to test extra attributes (lines 84-90)
    // stringifyAttributes converts to strings like "data-test=\"value\""
    // Then merged with existing attributes array
    $script = m::mock(Js::class);
    $script->shouldReceive('getPackage')->andReturn('filament');
    $script->shouldReceive('getId')->andReturn('app');
    $script->shouldReceive('getExtraAttributes')->andReturn(['existing' => 'attr']);
    $script->shouldReceive('extraAttributes')->once()->with(m::on(function (array $attributes): bool {
        // Should contain existing attribute and stringified attributes
        return isset($attributes['existing'])
            && in_array('data-test="value"', $attributes, true)
            && in_array('data-qa="true"', $attributes, true);
    }));

    FilamentAsset::shouldReceive('getScripts')->andReturn([$script]);
    FilamentAsset::shouldReceive('getAlpineComponents')->andReturn([]);

    $provider = new SupportCustomizationServiceProvider(App::getInstance());
    $provider->boot();

    $reflection = new ReflectionClass($provider);
    $method = $reflection->getMethod('configureFilamentAssets');
    $method->setAccessible(true);
    $method->invoke($provider);

    expect(true)->toBeTrue();
});

it('disables Alpine components when load_alpine is false', function (): void {
    Config::set('filament.assets.scripts', [
        'targets' => ['*'],
    ]);
    Config::set('filament.assets.load_alpine', false);

    // Mock Alpine component (lines 93-96)
    $component = m::mock(Filament\Support\Assets\AlpineComponent::class);
    $component->shouldReceive('loadedOnRequest')->once();

    FilamentAsset::shouldReceive('getScripts')->andReturn([]);
    FilamentAsset::shouldReceive('getAlpineComponents')->andReturn([$component]);

    $provider = new SupportCustomizationServiceProvider(App::getInstance());
    $provider->boot();

    $reflection = new ReflectionClass($provider);
    $method = $reflection->getMethod('configureFilamentAssets');
    $method->setAccessible(true);
    $method->invoke($provider);

    expect(true)->toBeTrue();
});

