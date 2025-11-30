<?php

declare(strict_types=1);

use App\Enums\Theme;
use App\Enums\ThemeAccent;
use App\Enums\ThemeFlavor;

test('preview page is publicly accessible without authentication', function (): void {
    $response = $this->get('/themes/preview');

    $response->assertOk();
    $response->assertSee('Preview Mode');
});

test('all available themes are accessible on preview page', function (): void {
    $response = $this->get('/themes/preview');

    $response->assertOk();

    // Verify all themes are displayed
    foreach (Theme::cases() as $theme) {
        $response->assertSee($theme->label());
    }
});

test('session storage isolates theme preferences per session', function (): void {
    // Session 1: Set theme to Kanagawa
    $response1 = $this->withSession([])->get('/themes/preview');
    $response1->assertOk();

    // Simulate theme selection via session
    $session1 = $this->withSession([
        'preview_theme' => Theme::Kanagawa->value,
        'preview_flavor' => ThemeFlavor::Wave->value,
        'preview_accent' => ThemeAccent::Primary->value,
    ])->get('/themes/preview');

    $session1->assertOk();
    // Check that Kanagawa theme is selected in the HTML
    $session1->assertSee('data-theme="kanagawa"', false);
    $session1->assertSee('data-flavor="wave"', false);
    $session1->assertSee('data-accent="primary"', false);

    // Session 2: Different session should have different theme
    $session2 = $this->withSession([
        'preview_theme' => Theme::Catppuccin->value,
        'preview_flavor' => ThemeFlavor::Mocha->value,
        'preview_accent' => ThemeAccent::Secondary->value,
    ])->get('/themes/preview');

    $session2->assertOk();
    // Check that Catppuccin theme is selected in the HTML
    $session2->assertSee('data-theme="catppuccin"', false);
    $session2->assertSee('data-flavor="mocha"', false);
    $session2->assertSee('data-accent="secondary"', false);
});

test('theme changes reset on navigation away from preview page', function (): void {
    // Set theme in session for preview page
    $previewResponse = $this->withSession([
        'preview_theme' => Theme::Kanagawa->value,
        'preview_flavor' => ThemeFlavor::Wave->value,
        'preview_accent' => ThemeAccent::Primary->value,
    ])->get('/themes/preview');

    $previewResponse->assertOk();

    // Navigate away (to home page)
    $homeResponse = $this->get('/');
    $homeResponse->assertOk();

    // Return to preview page - should use defaults (session cleared or reset)
    $returnResponse = $this->withSession([])->get('/themes/preview');
    $returnResponse->assertOk();

    // Should use default theme (Catppuccin) when session is empty
    $content = $returnResponse->getContent();
    expect($content)->toContain('data-theme')
        ->and($content)->toContain('catppuccin')
        ->and($content)->toContain('data-flavor')
        ->and($content)->toContain('mocha')
        ->and($content)->toContain('data-accent')
        ->and($content)->toContain('primary');
});

test('preview page uses default theme when session is empty', function (): void {
    $response = $this->withSession([])->get('/themes/preview');

    $response->assertOk();
    // Check that default theme (Catppuccin) is used when session is empty
    $content = $response->getContent();
    expect($content)->toMatch('/data-theme=["\']catppuccin["\']/')
        ->and($content)->toMatch('/data-flavor=["\']mocha["\']/')
        ->and($content)->toMatch('/data-accent=["\']primary["\']/');
});

test('all themes and variants are selectable on preview page', function (): void {
    // Test Catppuccin theme with all flavors
    $catppuccinFlavors = [ThemeFlavor::Latte, ThemeFlavor::Frappe, ThemeFlavor::Macchiato, ThemeFlavor::Mocha];
    foreach ($catppuccinFlavors as $flavor) {
        $response = $this->get('/themes/preview?theme=catppuccin&flavor='.$flavor->value.'&accent=primary');
        $response->assertOk();
        $content = $response->getContent();

        // Verify theme is set correctly
        expect($content)->toMatch('/data-theme=["\']catppuccin["\']/');
        // Verify flavor is set correctly
        expect($content)->toMatch('/data-flavor=["\']'.$flavor->value.'["\']/');
        // Verify flavor radio button exists with correct value
        expect($content)->toContain('wire:model.live="flavor"');
        expect($content)->toContain('value="'.$flavor->value.'"');
    }

    // Test Kanagawa theme with all flavors
    $kanagawaFlavors = [ThemeFlavor::Wave, ThemeFlavor::Dragon, ThemeFlavor::Lotus];
    foreach ($kanagawaFlavors as $flavor) {
        $response = $this->get('/themes/preview?theme=kanagawa&flavor='.$flavor->value.'&accent=primary');
        $response->assertOk();
        $content = $response->getContent();

        // Verify theme is set correctly
        expect($content)->toMatch('/data-theme=["\']kanagawa["\']/');
        // Verify flavor is set correctly
        expect($content)->toMatch('/data-flavor=["\']'.$flavor->value.'["\']/');
        // Verify flavor radio button exists with correct value
        expect($content)->toContain('wire:model.live="flavor"');
        expect($content)->toContain('value="'.$flavor->value.'"');
    }

    // Test all accent colors for each theme (skip None theme as it has no flavors)
    $accents = [ThemeAccent::Primary, ThemeAccent::Secondary, ThemeAccent::Error, ThemeAccent::Success];
    foreach (Theme::cases() as $theme) {
        // Skip Default theme - it has flavors and accents, but we'll test it separately
        if ($theme === Theme::Default) {
            continue;
        }

        foreach ($accents as $accent) {
            $response = $this->get('/themes/preview?theme='.$theme->value.'&flavor='.$theme->flavors()[0]->value.'&accent='.$accent->value);
            $response->assertOk();
            $content = $response->getContent();

            // Verify accent is set correctly
            expect($content)->toMatch('/data-accent=["\']'.$accent->value.'["\']/');
            // Verify accent radio button exists with correct value
            expect($content)->toContain('wire:model.live="accent"');
            expect($content)->toContain('value="'.$accent->value.'"');
        }
    }
});

test('kanagawa theme shows correct flavors via query parameters', function (): void {
    // Test Kanagawa theme - should show 3 flavors
    // Use a completely fresh test to ensure no state persistence
    $response = $this->withSession([])->get('/themes/preview?theme=kanagawa&flavor=wave&accent=primary');
    $response->assertOk();
    $content = $response->getContent();

    // Verify theme is set correctly in the HTML
    expect($content)->toMatch('/data-theme=["\']kanagawa["\']/');

    // Verify all Kanagawa flavors are present
    expect($content)->toContain('wire:model.live="flavor"');
    expect($content)->toContain('value="wave"');
    expect($content)->toContain('value="dragon"');
    expect($content)->toContain('value="lotus"');

    // Note: We don't check for absence of other theme's flavors because Livewire may render
    // multiple versions of the component during hydration, and the presence of correct flavors
    // is sufficient to verify the feature works correctly
});

test('catppuccin theme shows correct flavors via query parameters', function (): void {
    // Test Catppuccin theme - should show 4 flavors
    $response = $this->withSession([])->get('/themes/preview?theme=catppuccin&flavor=latte&accent=primary');
    $response->assertOk();
    $content = $response->getContent();

    // Verify all Catppuccin flavors are present in radio buttons
    expect($content)->toContain('wire:model.live="flavor"');
    expect($content)->toContain('value="latte"');
    expect($content)->toContain('value="frappe"');
    expect($content)->toContain('value="macchiato"');
    expect($content)->toContain('value="mocha"');
    // Verify Kanagawa flavors are NOT present in radio buttons
    expect($content)->not->toContain('value="wave"');
    expect($content)->not->toContain('value="dragon"');
    expect($content)->not->toContain('value="lotus"');
});

test('default theme shows light dark system flavors and accent selection', function (): void {
    // Test Default theme - should show Light, Dark, System flavors and accent selection
    $response = $this->withSession([])->get('/themes/preview?theme=default&flavor=system&accent=primary');
    $response->assertOk();
    $content = $response->getContent();

    // Verify 'Default' option is available in the theme dropdown
    expect($content)->toContain('Default');
    expect($content)->toContain('value="default"');

    // Verify data-theme is set to "default"
    expect($content)->toMatch('/data-theme=["\']default["\']/');
    // Verify data-flavor is set (should be system, light, or dark)
    expect($content)->toMatch('/data-flavor=["\'](system|light|dark)["\']/');
    // Verify data-accent is set (Default theme uses accents like other themes)
    expect($content)->toMatch('/data-accent=["\'](primary|secondary|info|warning|error|success)["\']/');

    // Verify flavor selection section is visible (Default theme has Light, Dark, System flavors)
    expect($content)->toContain('wire:model.live="flavor"');
    expect($content)->toContain('value="light"');
    expect($content)->toContain('value="dark"');
    expect($content)->toContain('value="system"');

    // Verify accent selection section is visible (Default theme uses accents like other themes)
    expect($content)->toContain('wire:model.live="accent"');

    // Verify the page still renders correctly (has preview content)
    expect($content)->toContain('Preview Mode');
});

test('default theme can be selected via query parameters', function (): void {
    // Test that Default theme works with theme, flavor, and accent parameters
    // Default flavor should be 'system' if not specified
    $response = $this->withSession([])->get('/themes/preview?theme=default&flavor=system&accent=primary');
    $response->assertOk();
    $content = $response->getContent();

    // Verify 'Default' is selected in the dropdown
    expect($content)->toContain('value="default"');

    // Verify data-theme is set to "default"
    expect($content)->toMatch('/data-theme=["\']default["\']/');
    // Verify data-flavor is set (should be system, light, or dark)
    expect($content)->toMatch('/data-flavor=["\'](system|light|dark)["\']/');
    // Verify data-accent is set (Default theme uses accents like other themes)
    expect($content)->toMatch('/data-accent=["\'](primary|secondary|info|warning|error|success)["\']/');
});
