# Filament Livewire v4 Compatibility Fix

## Problem

Filament v5.x-dev was using `Livewire\Mechanisms\ComponentRegistry` which no longer exists in Livewire v4.0.0-beta.3. This caused a `BindingResolutionException` during composer operations:

```
Target class [Livewire\Mechanisms\ComponentRegistry] does not exist.
```

## Solution

The `ComponentRegistry` class has been removed in Livewire v4. Component names are now resolved through the Livewire factory service.

### Changes Made

**File:** `vendor/filament/filament/src/Panel/Concerns/HasComponents.php`

1. Removed the import: `use Livewire\Mechanisms\ComponentRegistry;`
2. Updated the `queueLivewireComponentForRegistration()` method to use the factory:

```php
// Before:
$componentName = app(ComponentRegistry::class)->getName($component);

// After:
$componentName = app('livewire.factory')->resolveComponentName($component);
```

## Application

This patch is automatically applied via composer when dependencies are installed or updated. The patch is defined in `composer.json`:

```json
{
  "extra": {
    "patches": {
      "filament/filament": {
        "Fix Livewire v4 ComponentRegistry compatibility": "patches/filament-filament/livewire-v4-compatibility.patch"
      }
    }
  }
}
```

## Status

This is a temporary fix until Filament officially supports Livewire v4.0.0-beta.3. Once Filament releases an official update, this patch can be removed.

## Testing

After applying this patch:
- ✅ Composer operations complete successfully
- ✅ Laravel application boots correctly
- ✅ Package discovery works
- ✅ Filament components register properly
