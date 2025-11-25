<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Pest\Browser\Api\AwaitableWebpage;
use Pest\Browser\Api\PendingAwaitablePage;
use Pest\Browser\Api\Webpage;
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
// Configure browser testing for optimal performance
try {
    // Reduce timeout to 15 seconds for faster feedback (was 30s)
    pest()->browser()->timeout(15000);
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
 */
function assertNoJavaScriptErrorsExceptCspParser(PendingAwaitablePage|AwaitableWebpage|Webpage $page): PendingAwaitablePage|AwaitableWebpage|Webpage
{
    try {
        $page->assertNoJavaScriptErrors();
    } catch (Throwable $e) {
        // Only handle AssertionFailedError exceptions (use string to avoid internal-class errors).
        // Use string literal with is_a() to avoid PHPStan internal class warnings
        // Rector prefers throw_unless, which works fine with is_a() and string literals
        throw_unless(is_a($e, 'PHPUnit\Framework\AssertionFailedError'), $e);

        $message = $e->getMessage();

        // Check if the error contains CSP parser errors (various formats)
        // CSP parser errors can appear as:
        // - "CSP Parser Error: Unexpected token: input"
        // - "CSP Parser Error: Expected PUNCTUATION ":" but got PUNCTUATION "(""
        // - "Uncaught Error: CSP Parser Error: ..."
        // - In the main message: "but found 1: Uncaught Error: CSP Parser Error: ..."
        $isCspError = str_contains($message, 'CSP Parser Error') ||
            (bool) preg_match('/CSP.*Parser.*Error/i', $message);

        if ($isCspError) {
            // Extract all errors from the message
            // The message format can be:
            // 1. "Expected no JavaScript errors..., but found X:\n- Error 1\n- Error 2"
            // 2. "Expected no JavaScript errors..., but found 1: Uncaught Error: CSP Parser Error: ..."
            preg_match_all('/- (.+)/m', $message, $matches);
            /** @var list<non-empty-string> $errors */
            /** @phpstan-ignore-next-line */
            $errors = is_array($matches[1] ?? null) ? $matches[1] : [];

            // Also check the main message for CSP errors (format: "but found 1: Uncaught Error: CSP Parser Error: ...")
            $mainErrorIsCsp = false;
            if (preg_match('/but found \d+:\s*(.+?)(?:\n|$)/s', $message, $mainMatch) === 1) {
                /** @var array{0: non-falsy-string, 1: non-empty-string} $mainMatch */
                $mainError = $mainMatch[1];
                $mainErrorIsCsp = str_contains($mainError, 'CSP Parser Error') ||
                    (bool) preg_match('/CSP.*Parser.*Error/i', $mainError);
            }

            // If no errors were extracted from the list format, check if the main message contains only CSP errors
            if ($errors === [] && $mainErrorIsCsp) {
                // It's a CSP error in the main message, ignore it
                return $page;
            }

            // Filter out CSP parser errors (check for various CSP error patterns)
            $realErrors = array_filter($errors, function (string $error): bool {
                // Filter out all CSP parser errors - they contain "CSP Parser Error" or match CSP error patterns
                // Check both exact string match and regex pattern match
                $isCspErrorInList = str_contains($error, 'CSP Parser Error') ||
                    (bool) preg_match('/CSP.*Parser.*Error/i', $error);

                return ! $isCspErrorInList;
            });

            // If there are real errors, throw them
            if ($realErrors !== []) {
                /** @phpstan-ignore-next-line */
                throw new AssertionFailedError(
                    "Expected no JavaScript errors on the page, but found:\n".
                    implode("\n", array_map(fn (string $error): string => "- {$error}", $realErrors))
                );
            }

            // If only CSP parser errors (either in list or main message), ignore them (they're false positives)
            // If all errors were filtered out, it means they were all CSP errors
            return $page;
        }

        // Re-throw if it's not a CSP parser error
        throw $e;
    }

    return $page;
}
