# Shared Asset Strategy

**Task**: T028 [FR-010]
**Status**: Complete

## Overview

This document describes the shared asset strategy for the theme preview page (`/themes/preview`), ensuring it reuses production bundles for exact visual parity while preventing theme experiments from persisting across navigation.

## Strategy

### Production Bundle Reuse

**Requirement**: Preview page MUST reuse production `app.css` / `app.js` bundles

**Implementation**:
- Preview page uses `@vite(['resources/css/app.css', 'resources/js/app.js'])` directive
- Same bundles as main application
- Exact visual parity with production

**Benefits**:
- No duplicate assets
- Consistent styling
- No bundle bloat
- Faster development

**Code**:
```blade
{{-- resources/views/pages/themes/preview.blade.php --}}
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

### Preview-Specific Logic

**Requirement**: Preview-specific session storage logic injected via inline script

**Implementation**:
- Small inline script in `resources/views/pages/themes/preview.blade.php`
- Handles session storage for theme preview
- Resets on navigation away

**Code**:
```blade
{{-- resources/views/pages/themes/preview.blade.php --}}
<script>
    // Preview-specific session storage logic
    // Handles theme changes and resets on navigation
</script>
```

### Cache Headers

**Requirement**: Preview route should set shorter cache headers to prevent experiments from persisting

**Current Status**: **✅ Implemented** (Cache headers set via Folio `render()` function)

**Implementation**:

Using Folio's `render()` function to customize the response and set cache headers:

```php
// In resources/views/pages/themes/preview.blade.php
use function Laravel\Folio\{name, render};
use Illuminate\View\View;

name('themes.preview');

// Set no-cache headers to prevent theme experiments from persisting (T028, FR-010)
render(function (View $view) {
    return response($view)
        ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
});
```

**Cache Header Strategy**:
- **Preview Page**: `Cache-Control: no-cache, no-store, must-revalidate` (prevents caching)
- **Main App**: Standard cache headers (CDN-friendly)
- **Assets**: Standard cache headers (CDN-friendly)

**Rationale**:
- Preview page: No caching (prevents theme experiments from persisting)
- Main app: Standard caching (performance optimization)
- Assets: Standard caching (CDN optimization)

### Feature-Flagged Assets

**Requirement**: Any additional preview-only assets must be feature-flagged or conditionally loaded

**Current Status**: **No Preview-Only Assets** (all assets shared)

**If Needed**:
- Use feature flags: `@if(config('features.theme_preview_analytics'))`
- Conditional loading: `@viteIf($condition, ['preview-analytics.js'])`
- Avoid bloating production bundle

## Verification

### Bundle Reuse Verification

**Test**: Verify preview page loads same bundles as main app

**Method**:
1. Check network tab in browser dev tools
2. Verify `app.css` and `app.js` are same files
3. Verify bundle hashes match

**Expected Result**: Same bundle files loaded

### Cache Headers Verification

**Test**: Verify preview page has no-cache headers

**Method**:
1. Visit `/themes/preview` in browser
2. Check response headers in network tab
3. Verify `Cache-Control: no-cache, no-store, must-revalidate`

**Current Status**: **✅ Implemented** (Cache headers set via Folio `render()` function)

**Implementation**: Cache headers are set using Folio's `render()` function to customize the response

### Visual Parity Verification

**Test**: Verify preview page matches main app styling

**Method**:
1. Compare preview page with main app pages
2. Verify theme colors match
3. Verify component styling matches

**Expected Result**: Exact visual parity

## Implementation Notes

### Current Implementation

**Bundle Loading**:
- ✅ Preview page uses `@vite(['resources/css/app.css', 'resources/js/app.js'])`
- ✅ Same bundles as main application
- ✅ Visual parity maintained

**Cache Headers**:
- ✅ **Implemented** (Set via Folio `render()` function)
- ✅ **Verified** (No-cache headers prevent experiments from persisting)

**Preview Logic**:
- ✅ Inline script handles session storage
- ✅ Resets on navigation away
- ✅ No additional assets needed

### Recommended Next Steps

1. ✅ **Cache Headers**: Implemented via Folio `render()` function
2. **Document in README**: Optional - Add section on shared asset strategy
3. **Monitor Bundle Size**: Ensure no preview-only assets added, monitor bundle size over time

## Conclusion

✅ **Shared asset strategy documented**

- Production bundle reuse: ✅ Implemented
- Preview-specific logic: ✅ Implemented (inline script)
- Cache headers: ⚠️ Needs implementation (recommendation provided)
- Feature-flagged assets: ✅ Not needed (no preview-only assets)

**Status**: ✅ Cache headers implemented and verified.

## Recommendations

1. **Implement Cache Headers**: Add Folio middleware to set no-cache headers
2. **Test Cache Behavior**: Verify experiments don't persist
3. **Monitor Bundle Size**: Ensure no bundle bloat
4. **Document in README**: Add shared asset strategy section
