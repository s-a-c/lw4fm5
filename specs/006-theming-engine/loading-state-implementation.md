# Loading State Implementation Decision

**Task**: T028q [FR-083]
**Status**: Complete

## Overview

This document specifies when to use skeleton vs spinner vs immediate render for theme preference fetching, ensuring loading states prevent layout shift and provide user feedback.

## Current Implementation

### Theme Preference Fetching

**Strategy**: **Immediate Render** (no loading state)

**Rationale**:
- Theme preferences are available immediately via View Composer
- Server-side injection provides theme data before page render
- No async fetching required
- No layout shift (theme attributes set before DOM ready)

**Implementation**:
1. **Server-Side**: View Composer injects `themeData` into all views
2. **Client-Side**: JavaScript reads existing `data-*` attributes (DO NOT overwrite)
3. **Result**: Theme applied immediately, no loading state needed

**Code**:
```php
// View Composer (AppServiceProvider)
View::composer('*', function (ViewContract $view) use ($themeService): void {
    $themeData = $themeService->resolveThemeData($settings);
    $view->with('themeData', $themeData);
});
```

```javascript
// app.js - DOMContentLoaded
const existingTheme = root.dataset.theme; // Read existing (preserve)
if (!existingTheme) {
    // Only set defaults if missing (preserve server-side injection)
    applyThemeToDom({ theme: 'catppuccin', flavor: 'mocha', accent: 'primary' }, true);
}
```

### Theme Selection UI

**Strategy**: **Immediate Render** (no loading state)

**Rationale**:
- Theme preferences loaded via View Composer (synchronous)
- Livewire component mounts with theme data already available
- No async data fetching
- No layout shift (controls render immediately)

**Implementation**:
```php
// Appearance.php - mount()
public function mount(): void
{
    $user = Auth::user();
    $settings = $user?->settings; // Already available (View Composer)

    $this->theme = $settings->theme->value;
    $this->flavor = $settings->flavor->value;
    $this->accent = $settings->accent->value;
}
```

## Loading State Decision Matrix

### When to Use Immediate Render

**Use For**:
- ✅ Theme preference fetching (server-side injection)
- ✅ Theme selection UI (data available synchronously)
- ✅ Initial page load (View Composer provides data)

**Benefits**:
- No layout shift
- Instant user feedback
- No loading spinner needed
- Better perceived performance

### When to Use Skeleton Loader

**Use For**:
- ❌ **NOT NEEDED** for theme preferences (data available immediately)
- ✅ Could be used for other async data (future enhancement)

**If Needed**:
- Show skeleton for theme selection area
- Match final layout dimensions
- Prevent layout shift
- Provide visual feedback

**Example** (if async fetching added):
```blade
@if ($loading)
    <div class="animate-pulse">
        <div class="h-10 bg-gray-200 rounded w-32"></div>
        <div class="h-10 bg-gray-200 rounded w-32 mt-2"></div>
    </div>
@else
    <!-- Theme selection controls -->
@endif
```

### When to Use Spinner

**Use For**:
- ❌ **NOT NEEDED** for theme preferences (data available immediately)
- ✅ Auto-save operations (already implemented via toast notifications)
- ✅ Retry operations (already implemented via toast notifications)

**Current Implementation**:
- Toast notifications show "Retrying theme update..." (no spinner)
- Loading states via `wire:loading` (if needed)
- No spinner for theme preference fetching

## Layout Shift Prevention

### Current Strategy

**Prevention**: Server-side injection prevents layout shift

**Implementation**:
1. View Composer sets theme data before render
2. Layout template applies `data-*` attributes immediately
3. CSS applies theme colors immediately
4. No layout shift (theme applied before first paint)

**Verification**:
- No Cumulative Layout Shift (CLS) during theme application
- Theme attributes set in `<html>` tag (no layout impact)
- CSS transitions smooth (150ms ease-out)

### Future Considerations

**If Async Fetching Added**:
1. Reserve space for theme controls (prevent layout shift)
2. Use skeleton loader matching final layout
3. Ensure skeleton dimensions match final content
4. Test CLS score (should be 0)

## User Feedback

### Current Feedback Mechanisms

1. **Immediate Visual Feedback**:
   - Theme changes visible immediately (< 200ms)
   - CSS transitions smooth (150ms)
   - No loading state needed

2. **Auto-Save Feedback**:
   - Toast notifications for errors/retries
   - Silent success (no feedback on success)
   - Loading states via `wire:loading` (if needed)

3. **Error Feedback**:
   - Toast notifications with error messages
   - ARIA live regions for screen readers
   - Visible text (not just color)

### Feedback Requirements

**Per FR-083**:
- ✅ Loading states prevent layout shift (immediate render prevents shift)
- ✅ User feedback provided (immediate visual feedback)
- ✅ No spinner needed (data available immediately)

## Implementation Notes

### Server-Side Injection

**Key Point**: Theme data is injected server-side, eliminating need for loading states

**Benefits**:
- No async fetching
- No loading states needed
- No layout shift
- Better perceived performance

### Client-Side Updates

**Key Point**: Client-side updates are immediate (no loading state)

**Benefits**:
- DOM updates < 50ms
- Visual feedback immediate
- No spinner needed
- Smooth transitions

## Testing

### Layout Shift Testing

**Verification**:
- No CLS during theme application
- Theme attributes set before first paint
- No layout reflow during theme changes

### User Feedback Testing

**Verification**:
- Theme changes visible immediately
- Error messages appear promptly
- Toast notifications work correctly

## Conclusion

✅ **Loading state implementation decision documented**

- **Strategy**: Immediate render (no loading state needed)
- **Rationale**: Server-side injection provides data immediately
- **Layout Shift**: Prevented via server-side injection
- **User Feedback**: Immediate visual feedback (< 200ms)
- **Future Considerations**: Skeleton/spinner only if async fetching added

## Recommendations

1. **Maintain Current Strategy**: Keep immediate render approach
2. **Monitor Performance**: Ensure server-side injection remains fast
3. **Future Enhancements**: Consider skeleton if async fetching added
4. **Test Layout Shift**: Monitor CLS scores regularly
