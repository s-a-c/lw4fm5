<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Pest\Browser\Api\AwaitableWebpage;
use Pest\Browser\Api\PendingAwaitablePage;
use PHPUnit\Framework\AssertionFailedError;
use Tests\TestCase;

/**
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
 */
// Configure browser testing timeout to prevent hanging (30 seconds = 30000ms)
try {
    pest()->browser()->timeout(30000);
} catch (Throwable $e) {
    // Browser plugin may not be loaded, ignore
}

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Browser');

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Unit');

pest()->extend(TestCase::class)
    ->in('Architecture');

/**
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
 */
expect()->extend('toBeOne', fn () =>
    /** @phpstan-ignore-next-line */
    $this->toBe(1));

/**
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
 */
/**
 * Assert no JavaScript errors, filtering out known false-positive CSP parser errors.
 *
 * This helper filters out "CSP Parser Error" messages which are false positives
 * from the browser's CSP parser when no actual CSP headers are set.
 *
 * @param  PendingAwaitablePage|AwaitableWebpage  $page
 * @return PendingAwaitablePage|AwaitableWebpage
 */
function assertNoJavaScriptErrorsExceptCspParser($page)
{
    try {
        $page->assertNoJavaScriptErrors();
    } catch (AssertionFailedError $e) {
        $message = $e->getMessage();

        // Check if the error is only CSP parser errors
        if (str_contains($message, 'CSP Parser Error')) {
            // Extract all errors from the message
            preg_match_all('/- (.+)/', $message, $matches);
            $errors = $matches[1] ?? [];

            // Filter out CSP parser errors
            $realErrors = array_filter($errors, fn ($error): bool => ! str_contains($error, 'CSP Parser Error'));

            // If there are real errors, throw them
            if ($realErrors !== []) {
                throw new AssertionFailedError(
                    "Expected no JavaScript errors on the page, but found:\n".
                    implode("\n", array_map(fn (string $error): string => "- {$error}", $realErrors))
                );
            }

            // If only CSP parser errors, ignore them (they're false positives)
            return $page;
        }

        // Re-throw if it's not a CSP parser error
        throw $e;
    }

    return $page;
}
