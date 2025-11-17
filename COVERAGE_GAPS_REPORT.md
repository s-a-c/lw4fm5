# Coverage Gaps Analysis Report

**Current Coverage: ~97%+ (estimated)**
**Target: 100%**

**Last Updated:** Coverage fixes in progress

---

<details>
<summary>Expand for Tsble of Contents</summary>

- [Summary](#summary)
- [1. AppServiceProvider (86.1% coverage)](#1-appserviceprovider-861-coverage)
- [2. SupportCustomizationServiceProvider (98.5% coverage)](#2-supportcustomizationserviceprovider-985-coverage)
- [3. TelescopeServiceProvider (50.0% coverage)](#3-telescope-service-provider-500-coverage)
- [4. VoltServiceProvider (82.4% coverage)](#4-volt-service-provider-824-coverage)
- [5. PolicyChecksumMonitor (98.1% coverage)](#5-policy-checksum-monitor-981-coverage)
- [6. DependencyCatalogue (99.0% coverage)](#6-dependency-catalogue-990-coverage)
- [Priority Recommendations](#priority-recommendations)
- [Testing Strategy](#testing-strategy)
- [Next Steps](#next-steps)

</details>

---

## Summary

The test suite has 153 passing tests with 405 assertions. The following files have coverage gaps that need to be addressed:

---

## 1. AppServiceProvider (86.1% coverage)

**Missing Lines: 90-94, 91-93**

### Code Location
```90:94:app/Providers/AppServiceProvider.php
        } else {
            Password::defaults(fn () => Password::min(8)
                ->letters()
                ->numbers()
                ->symbols()
                ->mixedCase());
        }
```

### Issue

The `else` branch (lines 90-94) is executed when the environment is `local`. The test exists but may not be properly setting the environment to trigger this path.

### Required Test

- Test that when `app.env` is `local`, password defaults are configured with `min(8)` and without `uncompromised()`
- The test must actually execute the `configurePasswordRules()` method with `local` environment

### Current Test Status

- ✅ **FIXED**: Test updated to use reflection with mocked Application instance that returns `true` for `environment('local')`
- Test now properly triggers lines 90-94 using reflection to call `configurePasswordRules()` with mocked app

---

## 2. SupportCustomizationServiceProvider (98.5% coverage)

**Missing Line: 30**

### Code Location

```29:31:app/Providers/Filament/SupportCustomizationServiceProvider.php
        Filament::serving(function (): void {
            $this->configureFilamentAssets();
        });
```

### Issue

Line 30 is the callback execution inside the `Filament::serving()` event. The test uses reflection to call `configureFilamentAssets()` directly, but doesn't actually trigger the serving event callback.

### Required Test

- Test that when `Filament::serving()` event is fired, the callback executes `configureFilamentAssets()`
- Need to actually fire the serving event, not just use reflection

### Current Test Status

- Test exists but uses reflection instead of triggering the actual event
- Need to fire the actual `Filament::serving()` event

---

## 3. TelescopeServiceProvider (50.0% coverage)

**Missing Lines: 27-43, 63**

### Code Location

```26:44:app/Providers/TelescopeServiceProvider.php
        Telescope::filter(function (IncomingEntry $entry) use ($isLocal): bool {
            if ($isLocal) {
                return true;
            }
            if ($entry->isReportableException()) {
                return true;
            }
            if ($entry->isFailedRequest()) {
                return true;
            }
            if ($entry->isFailedJob()) {
                return true;
            }
            if ($entry->isScheduledTask()) {
                return true;
            }

            return $entry->hasMonitoredTag();
        });
```

```62:63:app/Providers/TelescopeServiceProvider.php
        if ($this->app->environment('local')) {
            return;
        }
```

### Issue

- **Lines 27-43**: The filter function logic for non-local environments. The filter is registered but never actually executed with real `IncomingEntry` objects to test the various conditions.
- **Line 63**: Early return in `hideSensitiveRequestDetails()` when environment is local.

### Required Tests

1. **Filter logic (lines 27-43)**: Need to test the filter function with mocked `IncomingEntry` objects that:
   - Return `true` when `isLocal` is true (line 28)
   - Return `true` when `isReportableException()` is true (line 30-31)
   - Return `true` when `isFailedRequest()` is true (line 33-34)
   - Return `true` when `isFailedJob()` is true (line 36-37)
   - Return `true` when `isScheduledTask()` is true (line 39-40)
   - Return result of `hasMonitoredTag()` otherwise (line 43)

2. **Line 63**: Test that `hideSensitiveRequestDetails()` returns early when environment is local

### Current Test Status

- Tests exist but don't actually execute the filter function with entry objects
- Line 63 test exists but may not be properly triggering the early return

---

## 4. VoltServiceProvider (82.4% coverage)

**Missing Lines: 53, 67-68, 71, 74, 88, 100, 110, 122**

### Code Locations

**Line 53**: Empty directories return

```52:54:app/Providers/VoltServiceProvider.php
        if ($directories->isEmpty()) {
            return;
        }
```

**Lines 67-68**: Exception catch and continue

```65:69:app/Providers/VoltServiceProvider.php
                try {
                    $class = $componentResolver->resolve($alias, $allMountPaths);
                } catch (Throwable) {
                    continue;
                }
```

**Line 71**: Non-string class continue

```70:72:app/Providers/VoltServiceProvider.php
                if (! is_string($class)) {
                    continue;
                }
```

**Line 74**: Non-existent class continue

```73:75:app/Providers/VoltServiceProvider.php
                if (! class_exists($class)) {
                    continue;
                }
```

**Line 88**: Non-existent directory return

```87:89:app/Providers/VoltServiceProvider.php
        if (! is_dir($directory->path)) {
            return [];
        }
```

**Line 100**: Non-file continue

```99:101:app/Providers/VoltServiceProvider.php
            if (! $file->isFile()) {
                continue;
            }
```

**Line 110**: file_get_contents false continue

```108:111:app/Providers/VoltServiceProvider.php
            $contents = @file_get_contents($file->getPathname());
            if ($contents === false) {
                continue;
            }
```

**Line 122**: Empty alias continue

```121:123:app/Providers/VoltServiceProvider.php
            if ($alias === '') {
                continue;
            }
```

### Issue

These are edge cases in the Volt component discovery logic that require specific conditions:
- Empty mounted directories
- Component resolver throwing exceptions
- Non-string class resolution results
- Non-existent classes after resolution
- Non-existent directories
- Non-file items in directory iteration
- Failed file reads
- Empty aliases after processing

### Required Tests

Each edge case needs a test that creates the specific condition:
1. **Line 53**: Mock `MountedDirectories` to return empty collection
2. **Lines 67-68**: Mock `ComponentResolver` to throw exception
3. **Line 71**: Mock `ComponentResolver` to return non-string
4. **Line 74**: Mock `ComponentResolver` to return string that doesn't exist as class
5. **Line 88**: Use non-existent directory path
6. **Line 100**: Mock `SplFileInfo` to return `isFile() === false`
7. **Line 110**: Mock `file_get_contents` to return `false` (or use unreadable file)
8. **Line 122**: Create scenario where alias becomes empty string after processing

### Current Test Status

- Tests exist but may not be properly creating the conditions to trigger these code paths
- Need more specific mocking or test file system setup

---

## 5. PolicyChecksumMonitor (98.1% coverage)

**Missing Line: 79**

### Code Location

```76:80:app/Console/Commands/PolicyChecksumMonitor.php
            $mismatched->each(function (mixed $entry): void {
                // Guard against non-array values to satisfy static analysis and runtime safety
                if (! is_array($entry)) {
                    return;
                }
```

### Issue

Line 79 is the early return when a mismatched entry is not an array. The test exists but may not be creating a scenario where `$mismatched` contains non-array values.

### Required Test

- Create a test where the mismatched entries collection contains non-array values (e.g., strings, objects, null)
- The test should trigger the command with config that produces non-array mismatched entries

### Current Test Status

- ⚠️ **PARTIAL**: Test exists that tests the guard logic in isolation (replicated code)
- Issue: The test doesn't actually execute the command code because PolicyChecksumMonitor is final and the collection is built internally
- The guard at line 79 is defensive code that's difficult to test without modifying the command structure
- Recommendation: Consider making the guard logic testable via dependency injection or accept that this defensive code may not reach 100% coverage

---

## 6. DependencyCatalogue (99.0% coverage)

**Missing Line: 181**

### Code Location

```180:182:app/Services/BasePlatform/DependencyCatalogue.php
        $date = $this->lastReviewedAt instanceof Carbon
            ? $this->lastReviewedAt
            : Carbon::parse($this->lastReviewedAt->toDateTimeString());
```

### Issue

Line 181 is the `else` branch when `lastReviewedAt` is NOT a `Carbon` instance (i.e., it's `CarbonImmutable`). The test exists but may not be properly hitting this line.

### Required Test

- Test `reviewDeadline()` with `CarbonImmutable` instance as `lastReviewedAt`
- Ensure the code path executes line 181-182 (the else branch)

### Current Test Status

- ✅ **FIXED**: Added new test `it('handles reviewDeadline with Carbon lastReviewedAt')` that uses Carbon instance (not CarbonImmutable)
- This test covers line 181 (the if branch when lastReviewedAt is Carbon instance)
- Existing test covers line 182 (the else branch when lastReviewedAt is CarbonImmutable)

---

## Priority Recommendations

### High Priority (Easy to Fix)

1. **AppServiceProvider lines 90-94**: Fix environment setting in test
2. **DependencyCatalogue line 181**: Verify test is hitting the else branch
3. **PolicyChecksumMonitor line 79**: Ensure test creates non-array entries

### Medium Priority (Requires More Complex Mocking)

4. **SupportCustomizationServiceProvider line 30**: Fire actual Filament serving event
5. **VoltServiceProvider**: Create specific conditions for each edge case

### Lower Priority (Complex Integration Testing)

6. **TelescopeServiceProvider lines 27-43**: Test filter function with actual entry objects

---

## Testing Strategy

### For Simple Cases (AppServiceProvider, DependencyCatalogue)

- Use proper environment/config setup
- Verify the actual code path is executed

### For Complex Cases (VoltServiceProvider, TelescopeServiceProvider)

- Use dependency injection where possible
- Mock external dependencies (file system, component resolver)
- Create specific test conditions that trigger each edge case

### For Event-Based Cases (SupportCustomizationServiceProvider)

- Fire actual events instead of using reflection
- Or use dependency injection to make the method testable

---

## Next Steps

1. Fix AppServiceProvider test to properly set local environment
2. Verify DependencyCatalogue test hits line 181
3. Improve PolicyChecksumMonitor test to create non-array entries
4. Refactor SupportCustomizationServiceProvider to fire actual events or use DI
5. Create comprehensive VoltServiceProvider edge case tests with proper mocking
6. Add TelescopeServiceProvider filter tests with mocked IncomingEntry objects

---
