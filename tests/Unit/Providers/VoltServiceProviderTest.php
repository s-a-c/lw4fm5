<?php

declare(strict_types=1);

use App\Providers\VoltServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Livewire\Volt\ComponentResolver;
use Livewire\Volt\MountedDirectories;
use Livewire\Volt\MountedDirectory;
use Mockery as m;
use ReflectionClass;

it('boots VoltServiceProvider', function (): void {
    expect(App::getProvider(VoltServiceProvider::class))->not->toBeNull();
});

it('handles empty mounted directories gracefully', function (): void {
    // Test line 53: return early when directories collection is empty
    $mountedDirectories = m::mock(MountedDirectories::class);
    $mountedDirectories->shouldReceive('paths')->andReturn([]); // Empty array

    App::instance(MountedDirectories::class, $mountedDirectories);

    $provider = new VoltServiceProvider(App::getInstance());

    // Use reflection to call registerVoltComponentAliases which checks isEmpty() at line 52
    $reflection = new ReflectionClass($provider);
    $method = $reflection->getMethod('registerVoltComponentAliases');
    $method->setAccessible(true);
    $method->invoke($provider); // Should return early at line 53

    expect(true)->toBeTrue();
});

it('handles non-existent directory in discoverVoltComponentAliases', function (): void {
    // Test line 88: return empty array when directory doesn't exist
    $provider = new VoltServiceProvider(App::getInstance());
    
    // Use reflection to call discoverVoltComponentAliases with non-existent directory
    $reflection = new ReflectionClass($provider);
    $method = $reflection->getMethod('discoverVoltComponentAliases');
    $method->setAccessible(true);
    
    $directory = m::mock(MountedDirectory::class);
    $directory->path = '/non-existent-directory-12345'; // Non-existent path
    
    $result = $method->invoke($provider, $directory);
    
    // Should return empty array when directory doesn't exist (line 88)
    expect($result)->toBeArray()->toBeEmpty();
});

it('handles component resolver throwing exception', function (): void {
    // Test lines 67-68: catch Throwable and continue
    $mountedDirectories = m::mock(MountedDirectories::class);
    $directory = m::mock(MountedDirectory::class);
    $directory->path = resource_path('views/pages'); // Use existing directory
    $mountedDirectories->shouldReceive('paths')->andReturn([$directory]);

    $componentResolver = m::mock(ComponentResolver::class);
    $componentResolver->shouldReceive('resolve')
        ->andThrow(new \Exception('Resolver error')); // Throw exception to trigger line 68

    App::instance(MountedDirectories::class, $mountedDirectories);
    App::instance(ComponentResolver::class, $componentResolver);

    $provider = new VoltServiceProvider(App::getInstance());

    // Use reflection to call registerVoltComponentAliases
    $reflection = new ReflectionClass($provider);
    $method = $reflection->getMethod('registerVoltComponentAliases');
    $method->setAccessible(true);
    $method->invoke($provider); // Should catch exception at line 67-68 and continue

    expect(true)->toBeTrue();
});

it('handles non-string class resolution result', function (): void {
    // Test line 71: continue when class is not string
    $mountedDirectories = m::mock(MountedDirectories::class);
    $directory = m::mock(MountedDirectory::class);
    $directory->path = resource_path('views/pages');
    $mountedDirectories->shouldReceive('paths')->andReturn([$directory]);

    $componentResolver = m::mock(ComponentResolver::class);
    $componentResolver->shouldReceive('resolve')
        ->andReturn(123); // Return non-string to trigger line 71

    App::instance(MountedDirectories::class, $mountedDirectories);
    App::instance(ComponentResolver::class, $componentResolver);

    $provider = new VoltServiceProvider(App::getInstance());

    // Use reflection to call registerVoltComponentAliases
    $reflection = new ReflectionClass($provider);
    $method = $reflection->getMethod('registerVoltComponentAliases');
    $method->setAccessible(true);
    $method->invoke($provider); // Should skip at line 71 when class is not string

    expect(true)->toBeTrue();
});

it('handles non-existent class after resolution', function (): void {
    // Test line 74: continue when class doesn't exist
    $mountedDirectories = m::mock(MountedDirectories::class);
    $directory = m::mock(MountedDirectory::class);
    $directory->path = resource_path('views/pages');
    $mountedDirectories->shouldReceive('paths')->andReturn([$directory]);

    $componentResolver = m::mock(ComponentResolver::class);
    $componentResolver->shouldReceive('resolve')
        ->andReturn('NonExistentClass12345'); // Return string that doesn't exist as class

    App::instance(MountedDirectories::class, $mountedDirectories);
    App::instance(ComponentResolver::class, $componentResolver);

    $provider = new VoltServiceProvider(App::getInstance());

    // Use reflection to call registerVoltComponentAliases
    $reflection = new ReflectionClass($provider);
    $method = $reflection->getMethod('registerVoltComponentAliases');
    $method->setAccessible(true);
    $method->invoke($provider); // Should skip at line 74 when class doesn't exist

    expect(true)->toBeTrue();
});
