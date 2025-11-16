<?php

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Create the dependency catalogue file using Laravel's Storage facade
// This matches how the test sets it up
\Illuminate\Support\Facades\Storage::disk('local')->put('base-platform/dependencies.json', json_encode([
    [
        'name' => 'laravel/framework',
        'version' => '12.0.0',
        'classification' => 'core',
        'owner' => 'Platform Engineering',
        'justification' => 'Application runtime and foundation',
        'lastReviewedAt' => '2025-07-01',
        'reviewCadence' => 'monthly',
        'riskLevel' => 'high',
        'notes' => 'Pinned to LTS release; breaking upgrades require ADR',
    ],
    [
        'name' => 'spatie/laravel-ignition',
        'version' => '2.1.4',
        'classification' => 'core',
        'owner' => 'Platform Engineering',
        'justification' => 'Error page handling and exception reporting',
        'lastReviewedAt' => '2025-10-31',
        'reviewCadence' => 'monthly',
        'riskLevel' => 'medium',
        'notes' => 'Track security advisories via composer audit',
    ],
], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));

// Mock the ComposerAuditRunnerContract
$app->instance(App\Contracts\BasePlatform\ComposerAuditRunnerContract::class, new class implements App\Contracts\BasePlatform\ComposerAuditRunnerContract {
    public function run(): Illuminate\Contracts\Process\ProcessResult
    {
        return Illuminate\Support\Facades\Process::result(
            output: json_encode(['advisories' => []], JSON_THROW_ON_ERROR),
            errorOutput: '',
            exitCode: 0,
        );
    }
});

$command = $app->make(App\Console\Commands\DependencyReviewReport::class);
$command->setLaravel($app);
$tester = new Symfony\Component\Console\Tester\CommandTester($command);
$exitCode = $tester->execute([]);

echo "exit code: {$exitCode}\n";
echo $tester->getDisplay();
