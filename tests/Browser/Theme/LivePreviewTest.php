<?php

declare(strict_types=1);

use App\Models\User;

use function Pest\Laravel\actingAs;

test('live preview updates html data attributes without reload', function (): void {
    actingAs(User::factory()->create());

    assertNoJavaScriptErrorsExceptCspParser(
        visit(route('appearance.edit'))
            ->waitForEvent('networkidle')
            ->assertScript('document.documentElement.dataset.theme', 'catppuccin')
            ->assertScript('document.documentElement.dataset.flavor', 'mocha')
            ->assertScript('document.documentElement.dataset.accent', 'primary')
            ->assertScript('window.__lastJsError ? window.__lastJsError.stack : null', null)
            ->assertScript('window.__lastJsError ? window.__lastJsError.message : null', null)
            ->assertScript('window.__lastJsError ? window.__lastJsError.filename : null', null)
            ->assertScript('window.__lastJsError ? window.__lastJsError.lineno : null', null)
            ->click('[data-test="appearance-theme-kanagawa"]')
            ->waitForEvent('networkidle')
            ->assertScript('window.__lastThemeEvent ? window.__lastThemeEvent.theme : null', 'kanagawa')
            ->assertScript('document.documentElement.dataset.theme', 'kanagawa')
            ->assertScript('document.documentElement.dataset.flavor', 'wave')
            ->click('[data-test="appearance-accent-blue"]')
            ->waitForEvent('networkidle')
            ->assertScript('document.documentElement.dataset.accent', 'blue')
    );
});
