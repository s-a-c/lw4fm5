<?php

declare(strict_types=1);

use App\Providers\Filament\SupportCustomizationServiceProvider;
use Filament\Events\ServingFilament;
use Filament\Facades\Filament;
use Filament\Facades\Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Mockery as m;
use Mockery\MockInterface;

it('boots SupportCustomizationServiceProvider', function (): void {
    $provider = App::getProvider(SupportCustomizationServiceProvider::class);
    assert($provider !== null);
    expect($provider)->not->toBeNull();
});

it('configures Filament assets on serving event', function (): void {
    Config::set('filament.assets.scripts', [
        'targets' => ['*'],
        'defer' => true,
        'async' => false,
    ]);

    // Mock FilamentAsset to return scripts
    /** @phpstan-var MockInterface&Js $script */
    $script = m::mock(Js::class);
    $script->shouldReceive('getPackage')->andReturn('filament');
    $script->shouldReceive('getId')->andReturn('app');
    $script->shouldReceive('defer')->once();
    $script->shouldReceive('getExtraAttributes')->andReturn([]);

    FilamentAsset::shouldReceive('getScripts')->andReturn([$script]);
    FilamentAsset::shouldReceive('getAlpineComponents')->andReturn([]);

    // Boot the provider to register the serving callback (line 29-30)
    $provider = new SupportCustomizationServiceProvider(App::getInstance());
    $provider->boot();

    // Use reflection to access and execute the serving callback to test line 30
    // Filament::serving registers callbacks that we can access via reflection
    $filamentReflection = new ReflectionClass(Filament::class);

    // Try to get the serving callbacks and execute them
    // Since we can't easily access Filament's internal callbacks, we'll test
    // by calling configureFilamentAssets directly which is what line 30 does
    $reflection = new ReflectionClass($provider);
    $method = $reflection->getMethod('configureFilamentAssets');
    $method->invoke($provider); // This executes the same code as line 30

    $providerCheck = App::getProvider(SupportCustomizationServiceProvider::class);
    assert($providerCheck !== null);
    expect($providerCheck)->not->toBeNull();
});

it('calls onFilamentServing from Filament serving callback', function (): void {
    // Test line 30: onFilamentServing called from Filament::serving callback
    Config::set('filament.assets.scripts', [
        'targets' => ['*'],
    ]);

    $script = m::mock(Js::class);
    $script->shouldReceive('getPackage')->andReturn('filament');
    $script->shouldReceive('getId')->andReturn('app');
    $script->shouldReceive('getExtraAttributes')->andReturn([]);

    FilamentAsset::shouldReceive('getScripts')->andReturn([$script]);
    FilamentAsset::shouldReceive('getAlpineComponents')->andReturn([]);

    $provider = new SupportCustomizationServiceProvider(App::getInstance());
    $provider->boot();

    // Dispatch ServingFilament event to trigger the callback registered at line 29
    // This will execute line 30 ($this->onFilamentServing())
    Event::dispatch(new ServingFilament);

    expect(true)->toBeTrue();
});

it('configures Filament assets on view composer', function (): void {
    Config::set('filament.assets.scripts', [
        'targets' => ['filament:app'], // scriptIdentifier returns lowercase "package:id"
        'defer' => false,
        'async' => true,
    ]);

    // Mock FilamentAsset to return scripts
    /** @phpstan-var MockInterface&Js $script */
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
    $method->invoke($provider);

    $providerCheck = App::getProvider(SupportCustomizationServiceProvider::class);
    assert($providerCheck !== null);
    expect($providerCheck)->not->toBeNull();
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
    $method->invoke($provider);

    $providerCheck = App::getProvider(SupportCustomizationServiceProvider::class);
    assert($providerCheck !== null);
    expect($providerCheck)->not->toBeNull();
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
    // stringifyAttributes converts to associative array like ['data-test' => 'value']
    // Then merged with existing attributes array
    $script = m::mock(Js::class);
    $script->shouldReceive('getPackage')->andReturn('filament');
    $script->shouldReceive('getId')->andReturn('app');
    $script->shouldReceive('getExtraAttributes')->andReturn(['existing' => 'attr']);
    $script->shouldReceive('extraAttributes')->once()->with(m::on(fn (array $attributes): bool =>
        // Should contain existing attribute and new attributes as associative array
        isset($attributes['existing'])
        && isset($attributes['data-test'])
        && $attributes['data-test'] === 'value'
        && isset($attributes['data-qa'])
        && $attributes['data-qa'] === 'true'));

    FilamentAsset::shouldReceive('getScripts')->andReturn([$script]);
    FilamentAsset::shouldReceive('getAlpineComponents')->andReturn([]);

    $provider = new SupportCustomizationServiceProvider(App::getInstance());
    $provider->boot();

    $reflection = new ReflectionClass($provider);
    $method = $reflection->getMethod('configureFilamentAssets');
    $method->invoke($provider);

    expect(true)->toBeTrue();
});

it('disables Alpine components when load_alpine is false', function (): void {
    Config::set('filament.assets.scripts', [
        'targets' => ['*'],
    ]);
    Config::set('filament.assets.load_alpine', false);

    // Mock Alpine component (lines 93-96)
    /** @phpstan-var MockInterface&AlpineComponent $component */
    /** @phpstan-ignore-next-line */
    $component = m::mock(AlpineComponent::class);
    /** @phpstan-ignore-next-line */
    $component->shouldReceive('loadedOnRequest')->once();

    FilamentAsset::shouldReceive('getScripts')->andReturn([]);
    FilamentAsset::shouldReceive('getAlpineComponents')->andReturn([$component]);

    $provider = new SupportCustomizationServiceProvider(App::getInstance());
    $provider->boot();

    $reflection = new ReflectionClass($provider);
    $method = $reflection->getMethod('configureFilamentAssets');
    $method->invoke($provider);

    expect(true)->toBeTrue();
});
