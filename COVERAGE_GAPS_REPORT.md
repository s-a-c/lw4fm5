# Coverage Gaps Analysis Report

**Current Coverage: ~97%+ (estimated)**
**Target: 100%**

**Last Updated:** Coverage fixes in progress

---

<details>
<summary>Expand for Tsble of Contents</summary>

- [Coverage Gaps Analysis Report](#coverage-gaps-analysis-report)
  - [1. Summary](#1-summary)
  - [2. AppServiceProvider (86.1% coverage)](#2-appserviceprovider-861-coverage)
    - [2.1. Code Location](#21-code-location)
    - [2.2. Issue](#22-issue)
    - [2.3. Required Test](#23-required-test)
    - [2.4. Current Test Status](#24-current-test-status)
  - [3. SupportCustomizationServiceProvider (98.5% coverage)](#3-supportcustomizationserviceprovider-985-coverage)
    - [3.1. Code Location](#31-code-location)
    - [3.2. Issue](#32-issue)
    - [3.3. Required Test](#33-required-test)
    - [3.4. Current Test Status](#34-current-test-status)
  - [4. TelescopeServiceProvider (50.0% coverage)](#4-telescopeserviceprovider-500-coverage)
    - [4.1. Code Location](#41-code-location)
    - [4.2. Issue](#42-issue)
    - [4.3. Required Tests](#43-required-tests)
    - [4.4. Current Test Status](#44-current-test-status)
  - [5. VoltServiceProvider (82.4% coverage)](#5-voltserviceprovider-824-coverage)
    - [5.1. Code Locations](#51-code-locations)
    - [5.2. Issue](#52-issue)
    - [5.3. Required Tests](#53-required-tests)
    - [5.4. Current Test Status](#54-current-test-status)
  - [6. PolicyChecksumMonitor (98.1% coverage)](#6-policychecksummonitor-981-coverage)
    - [6.1. Code Location](#61-code-location)
    - [6.2. Issue](#62-issue)
    - [6.3. Required Test](#63-required-test)
    - [6.4. Current Test Status](#64-current-test-status)
  - [7. DependencyCatalogue (99.0% coverage)](#7-dependencycatalogue-990-coverage)
    - [7.1. Code Location](#71-code-location)
    - [7.2. Issue](#72-issue)
    - [7.3. Required Test](#73-required-test)
    - [7.4. Current Test Status](#74-current-test-status)
  - [8. Priority Recommendations](#8-priority-recommendations)
    - [8.1. High Priority (Easy to Fix)](#81-high-priority-easy-to-fix)
    - [8.2. Medium Priority (Requires More Complex Mocking)](#82-medium-priority-requires-more-complex-mocking)
    - [8.3. Lower Priority (Complex Integration Testing)](#83-lower-priority-complex-integration-testing)
  - [9. Testing Strategy](#9-testing-strategy)
    - [9.1. For Simple Cases (AppServiceProvider, DependencyCatalogue)](#91-for-simple-cases-appserviceprovider-dependencycatalogue)
    - [9.2. For Complex Cases (VoltServiceProvider, TelescopeServiceProvider)](#92-for-complex-cases-voltserviceprovider-telescopeserviceprovider)
    - [9.3. For Event-Based Cases (SupportCustomizationServiceProvider)](#93-for-event-based-cases-supportcustomizationserviceprovider)
  - [10. Next Steps](#10-next-steps)

</details>

---

## 1. Summary

The test suite has 153 passing tests with 405 assertions. The following files have coverage gaps that need to be addressed:

---

## 2. AppServiceProvider (86.1% coverage)

**Missing Lines: 90-94, 91-93**

### 2.1. Code Location
```90:94:app/Providers/AppServiceProvider.php
        } else {
            Password::defaults(fn () => Password::min(8)
                ->letters()
                ->numbers()
                ->symbols()
                ->mixedCase());
        }
```

### 2.2. Issue

The `else` branch (lines 90-94) is executed when the environment is `local`. The test exists but may not be properly setting the environment to trigger this path.

### 2.3. Required Test

- Test that when `app.env` is `local`, password defaults are configured with `min(8)` and without `uncompromised()`
- The test must actually execute the `configurePasswordRules()` method with `local` environment

### 2.4. Current Test Status

- ✅ **FIXED**: Test updated to use reflection with mocked Application instance that returns `true` for `environment('local')`
- Test now properly triggers lines 90-94 using reflection to call `configurePasswordRules()` with mocked app

---

## 3. SupportCustomizationServiceProvider (98.5% coverage)

**Missing Line: 30**

### 3.1. Code Location

```29:31:app/Providers/Filament/SupportCustomizationServiceProvider.php
        Filament::serving(function (): void {
            $this->configureFilamentAssets();
        });
```

### 3.2. Issue

Line 30 is the callback execution inside the `Filament::serving()` event. The test uses reflection to call `configureFilamentAssets()` directly, but doesn't actually trigger the serving event callback.

### 3.3. Required Test

- Test that when `Filament::serving()` event is fired, the callback executes `configureFilamentAssets()`
- Need to actually fire the serving event, not just use reflection

### 3.4. Current Test Status

- Test exists but uses reflection instead of triggering the actual event
- Need to fire the actual `Filament::serving()` event

---

## 4. TelescopeServiceProvider (50.0% coverage)

**Missing Lines: 27-43, 63**

### 4.1. Code Location

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

### 4.2. Issue

- **Lines 27-43**: The filter function logic for non-local environments. The filter is registered but never actually executed with real `IncomingEntry` objects to test the various conditions.
- **Line 63**: Early return in `hideSensitiveRequestDetails()` when environment is local.

### 4.3. Required Tests

1. **Filter logic (lines 27-43)**: Need to test the filter function with mocked `IncomingEntry` objects that:
   - Return `true` when `isLocal` is true (line 28)
   - Return `true` when `isReportableException()` is true (line 30-31)
   - Return `true` when `isFailedRequest()` is true (line 33-34)
   - Return `true` when `isFailedJob()` is true (line 36-37)
   - Return `true` when `isScheduledTask()` is true (line 39-40)
   - Return result of `hasMonitoredTag()` otherwise (line 43)

2. **Line 63**: Test that `hideSensitiveRequestDetails()` returns early when environment is local

### 4.4. Current Test Status

- Tests exist but don't actually execute the filter function with entry objects
- Line 63 test exists but may not be properly triggering the early return

---

## 5. VoltServiceProvider (82.4% coverage)

**Missing Lines: 53, 67-68, 71, 74, 88, 100, 110, 122**

### 5.1. Code Locations

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

### 5.2. Issue

These are edge cases in the Volt component discovery logic that require specific conditions:
- Empty mounted directories
- Component resolver throwing exceptions
- Non-string class resolution results
- Non-existent classes after resolution
- Non-existent directories
- Non-file items in directory iteration
- Failed file reads
- Empty aliases after processing

### 5.3. Required Tests

Each edge case needs a test that creates the specific condition:
1. **Line 53**: Mock `MountedDirectories` to return empty collection
2. **Lines 67-68**: Mock `ComponentResolver` to throw exception
3. **Line 71**: Mock `ComponentResolver` to return non-string
4. **Line 74**: Mock `ComponentResolver` to return string that doesn't exist as class
5. **Line 88**: Use non-existent directory path
6. **Line 100**: Mock `SplFileInfo` to return `isFile() === false`
7. **Line 110**: Mock `file_get_contents` to return `false` (or use unreadable file)
8. **Line 122**: Create scenario where alias becomes empty string after processing

### 5.4. Current Test Status

- Tests exist but may not be properly creating the conditions to trigger these code paths
- Need more specific mocking or test file system setup

---

## 6. PolicyChecksumMonitor (98.1% coverage)

**Missing Line: 79**

### 6.1. Code Location

```76:80:app/Console/Commands/PolicyChecksumMonitor.php
            $mismatched->each(function (mixed $entry): void {
                // Guard against non-array values to satisfy static analysis and runtime safety
                if (! is_array($entry)) {
                    return;
                }
```

### 6.2. Issue

Line 79 is the early return when a mismatched entry is not an array. The test exists but may not be creating a scenario where `$mismatched` contains non-array values.

### 6.3. Required Test

- Create a test where the mismatched entries collection contains non-array values (e.g., strings, objects, null)
- The test should trigger the command with config that produces non-array mismatched entries

### 6.4. Current Test Status

- ⚠️ **PARTIAL**: Test exists that tests the guard logic in isolation (replicated code)
- Issue: The test doesn't actually execute the command code because PolicyChecksumMonitor is final and the collection is built internally
- The guard at line 79 is defensive code that's difficult to test without modifying the command structure
- Recommendation: Consider making the guard logic testable via dependency injection or accept that this defensive code may not reach 100% coverage

---

## 7. DependencyCatalogue (99.0% coverage)

**Missing Line: 181**

### 7.1. Code Location

```180:182:app/Services/BasePlatform/DependencyCatalogue.php
        $date = $this->lastReviewedAt instanceof Carbon
            ? $this->lastReviewedAt
            : Carbon::parse($this->lastReviewedAt->toDateTimeString());
```

### 7.2. Issue

Line 181 is the `else` branch when `lastReviewedAt` is NOT a `Carbon` instance (i.e., it's `CarbonImmutable`). The test exists but may not be properly hitting this line.

### 7.3. Required Test

- Test `reviewDeadline()` with `CarbonImmutable` instance as `lastReviewedAt`
- Ensure the code path executes line 181-182 (the else branch)

### 7.4. Current Test Status

- ✅ **FIXED**: Added new test `it('handles reviewDeadline with Carbon lastReviewedAt')` that uses Carbon instance (not CarbonImmutable)
- This test covers line 181 (the if branch when lastReviewedAt is Carbon instance)
- Existing test covers line 182 (the else branch when lastReviewedAt is CarbonImmutable)

---

## 8. Priority Recommendations

### 8.1. High Priority (Easy to Fix)

1. **AppServiceProvider lines 90-94**: Fix environment setting in test
2. **DependencyCatalogue line 181**: Verify test is hitting the else branch
3. **PolicyChecksumMonitor line 79**: Ensure test creates non-array entries

### 8.2. Medium Priority (Requires More Complex Mocking)

4. **SupportCustomizationServiceProvider line 30**: Fire actual Filament serving event
5. **VoltServiceProvider**: Create specific conditions for each edge case

### 8.3. Lower Priority (Complex Integration Testing)

6. **TelescopeServiceProvider lines 27-43**: Test filter function with actual entry objects

---

## 9. Testing Strategy

### 9.1. For Simple Cases (AppServiceProvider, DependencyCatalogue)

- Use proper environment/config setup
- Verify the actual code path is executed

### 9.2. For Complex Cases (VoltServiceProvider, TelescopeServiceProvider)

- Use dependency injection where possible
- Mock external dependencies (file system, component resolver)
- Create specific test conditions that trigger each edge case

### 9.3. For Event-Based Cases (SupportCustomizationServiceProvider)

- Fire actual events instead of using reflection
- Or use dependency injection to make the method testable

---

## 10. Next Steps

1. Fix AppServiceProvider test to properly set local environment
2. Verify DependencyCatalogue test hits line 181
3. Improve PolicyChecksumMonitor test to create non-array entries
4. Refactor SupportCustomizationServiceProvider to fire actual events or use DI
5. Create comprehensive VoltServiceProvider edge case tests with proper mocking
6. Add TelescopeServiceProvider filter tests with mocked IncomingEntry objects

---
