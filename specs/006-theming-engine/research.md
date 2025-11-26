# Research: Theming Engine

**Date**: 2025-11-25
**Feature**: Theming Engine Implementation
**Status**: Complete

## Overview

This document consolidates research findings and decisions for implementing the Theming Engine feature. All clarifications from the specification have been resolved through code review and architectural decisions.

## Technical Decisions

### 1. Server-Side Theme Injection Strategy

**Decision**: Hybrid approach - server-side injection for initial load, client-side updates for live preview.

**Rationale**:

- Server-side injection prevents FOUC (Flash of Unstyled Content) on initial page load
- Client-side updates enable instant live preview (<200ms requirement) without page reload
- Existing code already uses client-side JavaScript in `resources/js/app.js` for fallback initialization
- View Composers in Laravel provide the mechanism for global server-side injection

**Implementation**:

- Use `View::composer('*', ...)` in `AppServiceProvider` to inject `data-theme`, `data-flavor`, `data-accent` attributes into the `<html>` element
- Read user settings from authenticated user, validate, and provide defaults for unauthenticated users
- Client-side JavaScript in `resources/js/app.js` handles live updates via Livewire's `$this->js()` method

**Alternatives Considered**:

- **Server-side only**: Would require page reload for every theme change, violating <200ms requirement
- **Client-side only**: Would cause FOUC on initial load, poor user experience
- **Hybrid approach**: ✅ Best of both worlds - no FOUC, instant updates

### 2. CSS Variable Application Strategy

**Decision**: Use CSS attribute selectors with data attributes (`[data-theme="catppuccin"][data-flavor="mocha"]`).

**Rationale**:

- Existing CSS in `resources/css/app.css` already uses this pattern
- Integrates seamlessly with Tailwind CSS v3
- Supports both server-injected and client-updated attributes
- No need for dynamic CSS injection or class toggling

**Integration Requirements**:

- **Livewire Flux**: Uses 'zinc' color palette. CSS maps `--color-zinc-*` variables based on theme/flavor.
- **Filament**: Uses 'gray' color palette. CSS maps `--color-gray-*` to `--color-zinc-*` (the "Zinc Bridge").
- Both frameworks receive theme-aware colors through CSS variable inheritance.

**Implementation**:

- CSS attribute selectors in `@layer theme` in `resources/css/app.css`
- Each theme/flavor combination defines `--color-zinc-*` variables
- Accent colors defined as `--accent-*` variables (primary, blue, red, green)
- Dark mode class (`dark`) managed based on flavor (light flavors: latte, lotus)

**Alternatives Considered**:

- **CSS Classes**: Would require dynamic class manipulation, more complex state management
- **Dynamic CSS Injection**: Performance overhead, harder to maintain
- **CSS Attribute Selectors**: ✅ Declarative, performant, maintainable

### 3. Theme Validation and Auto-Correction

**Decision**: Validate theme/flavor/accent combinations on every access (whenever settings are read: View Composer, Livewire mount, direct model access, etc.), silently reset to default if invalid.

**Rationale**:

- Prevents broken UI from corrupted database data
- Handles enum changes gracefully (e.g., if a flavor is removed in future)
- User experience: silent correction is better than error messages for cosmetic settings
- Default theme: Catppuccin Mocha (as specified in spec)

**Implementation**:

- Validation occurs on every access (whenever settings are read: View Composer, Livewire mount, direct model access, etc.)
- Validation in `User` model's `booted()` method or `ThemeService` class
- Optional `ThemeService` class for centralized validation logic
- Validation checks:
  1. Theme enum is valid
  2. Flavor enum is valid
  3. Accent enum is valid
  4. Flavor belongs to selected Theme (e.g., Latte belongs to Catppuccin, not Kanagawa)
- On invalid: Reset to `Theme::Catppuccin`, `ThemeFlavor::Mocha`, `ThemeAccent::Primary`
- Persist corrected settings to database silently
- Performance impact is minimal since validation is lightweight

**Alternatives Considered**:

- **Show error message**: Poor UX for cosmetic settings, user may not understand the error
- **No validation**: Risk of broken UI from corrupted data
- **Silent auto-correction**: ✅ Best UX, prevents broken states

### 4. Global Theme Application

**Decision**: Apply theme globally via View Composer to all views, including Filament panels and auth pages.

**Rationale**:

- User expectation: theme should be consistent across entire application
- Filament panels are part of the application experience
- Auth pages (login, register) should respect user preferences for brand consistency
- View Composer with `'*'` pattern applies to all views automatically

**Implementation**:

- `View::composer('*', ...)` in `AppServiceProvider::boot()`
- Inject data attributes into view data
- Layout templates read data attributes and apply to `<html>` element
- Filament panels: Ensure Filament's layout templates receive theme data
- Auth pages: Fortify views already use Livewire components, will receive theme data

**Alternatives Considered**:

- **Selective application**: Would require maintaining list of views, error-prone
- **Global application**: ✅ Simple, consistent, maintainable

### 5. Demo Page Implementation

**Decision**: Public demo page uses session storage for temporary theme changes, resets on navigation away.

**Rationale**:

- Demo page is for showcasing themes, not for authenticated user preferences
- Session storage is appropriate for temporary, page-scoped changes
- Resets on navigation to prevent theme "leaking" to other pages
- No database writes for unauthenticated visitors

**Implementation**:

- Rename `resources/views/pages/tailwindcss.catppuccin.com/index.blade.php` to `resources/views/pages/themes/preview.blade.php`
- Route: `/themes/preview` (Folio auto-generated from file path)
- Add theme selection controls for both Catppuccin and Kanagawa themes
- Use JavaScript `sessionStorage` to store selected theme/flavor/accent
- On page load: Read from sessionStorage, apply to DOM
- On theme change: Update sessionStorage and DOM immediately
- On navigation away: Session storage persists but theme doesn't apply to other pages (they use user settings or defaults)

**Alternatives Considered**:

- **localStorage**: Would persist across sessions, not appropriate for demo
- **URL parameters**: Would require page reload, violates instant preview requirement
- **Session storage**: ✅ Temporary, page-scoped, no persistence beyond session

### 6. Live Preview and Auto-Save

**Decision**: Immediate live preview with auto-save (no explicit save button required).

**Rationale**:

- User expectation: Modern UI patterns (e.g., VS Code settings) auto-save immediately
- Reduces friction: No need to remember to click "Save"
- Existing code in `appearance.blade.php` already implements this pattern
- Livewire's `wire:model.live` provides instant reactivity

**Implementation**:

- `wire:model.live` on theme/flavor/accent radio buttons
- `updated()` method in Livewire component triggers on property change
- Save to database immediately via `$user->save()`
- Update DOM via `$this->js()` for instant visual feedback
- Optional: Dispatch Flux toast notification (already implemented)

**Alternatives Considered**:
- **Explicit save button**: Adds friction, violates <200ms requirement
- **Debounced auto-save**: Unnecessary complexity, immediate save is fast enough
- **Immediate auto-save**: ✅ Best UX, matches modern expectations

## Dependencies and Integration Points

### Existing Code to Leverage

1. **Enums**: `Theme`, `ThemeFlavor`, `ThemeAccent` - Already defined, no changes needed
2. **UserSettingsData**: Spatie Data DTO - Already defined, no changes needed
3. **User Model**: Already casts `settings` to `UserSettingsData`, has null check in `booted()`
4. **Appearance Settings UI**: Already implements live preview and auto-save
5. **CSS Theme System**: Already uses attribute selectors, defines theme variables

### Code to Modify

1. **AppServiceProvider**: Add View Composer for server-side theme injection
2. **resources/js/app.js**: Enhance client-side initialization (already has basic implementation)
3. **Theme Preview Page**: Rename to `/themes/preview` and update to support all themes with session storage

### Code to Create

1. **ThemeService** (optional): Centralized validation logic if validation becomes complex
2. **Tests**: Feature tests for theme persistence, validation, global application, theme preview page (TDD workflow)

## Performance Considerations

- **Server-side injection**: Minimal overhead (single database query per request, cached if user is already loaded)
- **Client-side updates**: DOM manipulation is fast (<200ms requirement easily met)
- **CSS attribute selectors**: Native browser performance, no JavaScript overhead
- **Session storage**: In-memory, no network requests

## Security Considerations

- **Validation**: Prevent invalid enum values from being persisted
- **Authorization**: Theme settings are user-specific, no cross-user access
- **XSS**: Data attributes are safe (Laravel escapes by default)
- **CSRF**: Livewire handles CSRF tokens automatically

## Testing Strategy

- **Unit Tests**: Theme validation logic, enum relationships
- **Feature Tests**: Theme persistence, global application, invalid combination handling
- **Browser Tests**: Live preview functionality, theme preview page behavior
- **Integration Tests**: Filament panel theme application, auth page theme application

## Open Questions Resolved

All clarifications from the specification have been resolved:

- ✅ Theme change timing: Immediate with auto-save
- ✅ Theme application scope: All pages globally
- ✅ Invalid combination handling: Silent auto-correction
- ✅ Server-side vs client-side: Hybrid approach
- ✅ CSS application: Attribute selectors
- ✅ Theme preview page behavior: Session storage, temporary, renamed to `/themes/preview`

## Next Steps

Proceed to Phase 1: Design & Contracts to generate:

- Data model documentation
- API contracts (if needed - this feature is primarily UI-driven)
- Quickstart guide
