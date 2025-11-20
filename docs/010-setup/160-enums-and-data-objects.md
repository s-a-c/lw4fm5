# Migration Plan: Constants to Enums & Readonly Classes to Laravel Data

-----

<details>
<summary>Expand for Table of Contents</summary>

- [Migration Plan: Constants to Enums \& Readonly Classes to Laravel Data](#migration-plan-constants-to-enums--readonly-classes-to-laravel-data)
  - [1. Executive Summary](#1-executive-summary)
  - [2. Current State Analysis](#2-current-state-analysis)
    - [2.1. Constants Identified](#21-constants-identified)
      - [2.1.1. Status Constants (3 sets)](#211-status-constants-3-sets)
      - [2.1.2. Dependency Catalogue Constants (`app/Services/BasePlatform/DependencyCatalogue.php`)](#212-dependency-catalogue-constants-appservicesbaseplatformdependencycataloguephp)
      - [2.1.3. Path Constants](#213-path-constants)
    - [2.2. Readonly Data Transfer Classes Identified](#22-readonly-data-transfer-classes-identified)
    - [2.3. Current Laravel Data Usage](#23-current-laravel-data-usage)
  - [3. Proposed Enum Structure](#3-proposed-enum-structure)
    - [3.1. Status Enums](#31-status-enums)
      - [3.1.1. Option A: Shared Status Enum (Recommended)](#311-option-a-shared-status-enum-recommended)
      - [3.1.2. Option B: Separate Status Enums (Alternative)](#312-option-b-separate-status-enums-alternative)
    - [3.2. Dependency Enums](#32-dependency-enums)
    - [3.3. Path Constants](#33-path-constants)
  - [4. Proposed Laravel Data Structure](#4-proposed-laravel-data-structure)
    - [4.1. ParityReportData](#41-parityreportdata)
    - [4.2. ProfileValidationResultData](#42-profilevalidationresultdata)
    - [4.3. BootstrapRunData](#43-bootstraprundata)
    - [4.4. DependencyRecordData](#44-dependencyrecorddata)
    - [4.5. BootstrapRecoveryGuidanceData](#45-bootstraprecoveryguidancedata)
  - [5. Migration Strategy](#5-migration-strategy)
    - [5.1. Phase 1: Create Enums (Low Risk)](#51-phase-1-create-enums-low-risk)
    - [5.2. Phase 2: Update Data Classes (Medium Risk)](#52-phase-2-update-data-classes-medium-risk)
    - [5.3. Phase 3: Database Migration (High Risk)](#53-phase-3-database-migration-high-risk)
    - [5.4. Phase 4: Remove Constants (Low Risk)](#54-phase-4-remove-constants-low-risk)
    - [5.5. Phase 5: Cleanup \& Documentation](#55-phase-5-cleanup--documentation)
  - [6. Detailed File Changes](#6-detailed-file-changes)
    - [6.1. Files to Create](#61-files-to-create)
    - [6.2. Files to Modify](#62-files-to-modify)
  - [7. Pros and Cons](#7-pros-and-cons)
    - [7.1. Pros](#71-pros)
      - [7.1.1. Type Safety (High Impact)](#711-type-safety-high-impact)
      - [7.1.2. Laravel Data Benefits (High Impact)](#712-laravel-data-benefits-high-impact)
      - [7.1.3. Code Quality (Medium Impact)](#713-code-quality-medium-impact)
      - [7.1.4. Maintainability (Medium Impact)](#714-maintainability-medium-impact)
    - [7.2. Cons](#72-cons)
      - [7.2.1. Migration Complexity (High Impact)](#721-migration-complexity-high-impact)
      - [7.2.2. Learning Curve (Low Impact)](#722-learning-curve-low-impact)
      - [7.2.3. Performance (Negligible)](#723-performance-negligible)
      - [7.2.4. Potential Issues (Medium Impact)](#724-potential-issues-medium-impact)
  - [8. Risk Assessment](#8-risk-assessment)
    - [8.1. Low Risk](#81-low-risk)
    - [8.2. Medium Risk](#82-medium-risk)
    - [8.3. High Risk](#83-high-risk)
  - [9. Testing Strategy](#9-testing-strategy)
    - [9.1. Unit Tests](#91-unit-tests)
    - [9.2. Integration Tests](#92-integration-tests)
    - [9.3. Regression Tests](#93-regression-tests)
  - [10. Recommendation](#10-recommendation)
    - [10.1. Overall Recommendation: **85% - Proceed with Phased Migration**](#101-overall-recommendation-85---proceed-with-phased-migration)
    - [10.2. Recommended Approach](#102-recommended-approach)
    - [10.3. Alternative: Hybrid Approach (90% Recommendation)](#103-alternative-hybrid-approach-90-recommendation)
  - [11. Implementation Checklist](#11-implementation-checklist)
    - [11.1. Pre-Implementation](#111-pre-implementation)
    - [11.2. Phase 1: Enums](#112-phase-1-enums)
    - [11.3. Phase 2: Data Classes](#113-phase-2-data-classes)
    - [11.4. Phase 3: Database (if needed)](#114-phase-3-database-if-needed)
    - [11.5. Phase 4: Cleanup](#115-phase-4-cleanup)
    - [11.6. Phase 5: Documentation](#116-phase-5-documentation)
  - [12. Questions for Clarification](#12-questions-for-clarification)
  - [13. References](#13-references)

</details>

-----

## 1. Executive Summary

This document outlines a comprehensive plan to modernize the codebase by:

1. Converting all string constants to backed PHP 8.1+ native enums
2. Converting readonly data transfer classes to `spatie/laravel-data` Data objects

**Recommendation: 85% - Proceed with phased migration**

The migration will improve type safety, reduce magic strings, enhance IDE support, and provide better serialization capabilities. However, it requires careful coordination due to database storage and API compatibility considerations.

-----

## 2. Current State Analysis

### 2.1. Constants Identified

#### 2.1.1. Status Constants (3 sets)

1. **ParityReport** (`app/Services/BasePlatform/ParityReport.php`)
   - `STATUS_PASS = 'pass'`
   - `STATUS_WARNING = 'warning'`
   - `STATUS_FAIL = 'fail'`

2. **ProfileValidationResult** (`app/Services/BasePlatform/ProfileValidationResult.php`)
   - `STATUS_PASS = 'pass'`
   - `STATUS_WARNING = 'warning'`
   - `STATUS_FAIL = 'fail'`

3. **BootstrapRun** (`app/Services/BasePlatform/BootstrapRun.php`)
   - `STATUS_SUCCESS = 'success'`
   - `STATUS_WARNING = 'warning'`
   - `STATUS_FAILED = 'failed'`

#### 2.1.2. Dependency Catalogue Constants (`app/Services/BasePlatform/DependencyCatalogue.php`)

4. **VALID_CLASSIFICATIONS** = `['core', 'optional', 'experimental']`
5. **VALID_REVIEW_CADENCES** = `['monthly', 'quarterly']`
6. **VALID_RISK_LEVELS** = `['high', 'medium', 'low']`

#### 2.1.3. Path Constants

7. **CATALOGUE_PATH** = `'base-platform/dependencies.json'` (DependencyCatalogue)
8. **REPORT_DIRECTORY** = `'base-platform/dependency-reports'` (DependencyReviewPerformanceReport)
9. **PERFORMANCE_LOG** = `'base-platform/dependency-performance.log'` (DependencyReviewPerformanceReport)
10. **DEFAULT_REPORT_DIRECTORY** = `'base-platform/dependency-reports'` (DependencyReviewReport)
11. **COMPOSER_AUDIT_COMMAND** = `'composer audit --format=json'` (ComposerAuditRunner)

### 2.2. Readonly Data Transfer Classes Identified

1. **ParityReport** (`app/Services/BasePlatform/ParityReport.php`)
   - Properties: `profile` (string), `status` (string), `issues` (array)
   - Has static method `persistMany()` for database persistence
   - Used in: ParityChecker, RunParityCheck command

2. **ProfileValidationResult** (`app/Services/BasePlatform/ProfileValidationResult.php`)
   - Properties: `profile` (string), `status` (string), `issues` (array)
   - Has helper methods: `isPass()`, `isWarning()`, `isFail()`
   - Used in: EnvironmentProfileValidator, ValidateEnvironmentProfiles command

3. **BootstrapRun** (`app/Services/BasePlatform/BootstrapRun.php`)
   - Properties: `profile` (string), `status` (string), `durationMinutes` (float), `notes` (array)
   - Has helper methods: `isSuccessful()`, `isWarning()`
   - Used in: BootstrapRunner, RunPlatformBootstrap command

4. **DependencyRecord** (`app/Services/BasePlatform/DependencyCatalogue.php`)
   - Properties: `name`, `version`, `classification`, `owner`, `justification`, `lastReviewedAt`, `reviewCadence`, `riskLevel`, `notes`
   - Has methods: `reviewDeadline()`, `isOverdue()`, `toArray()`
   - Used in: DependencyCatalogue, DependencyReviewReport command

5. **BootstrapRecoveryGuidance** (`app/Services/BasePlatform/BootstrapRecoveryGuidance.php`)
   - Properties: `title` (string), `documentation` (string), `nextSteps` (array)
   - Used in: BootstrapRecovery, BootstrapRunnerException, RunPlatformBootstrap command

### 2.3. Current Laravel Data Usage

- **UserSettingsData** (`app/Data/UserSettingsData.php`) - Already using `spatie/laravel-data`
- Package installed: `spatie/laravel-data: ^4.18` (confirmed in composer.json)

-----

## 3. Proposed Enum Structure

### 3.1. Status Enums

#### 3.1.1. Option A: Shared Status Enum (Recommended)

```php
enum Status: string
{
    case Pass = 'pass';
    case Warning = 'warning';
    case Fail = 'fail';
    case Success = 'success';
    case Failed = 'failed';
}

```

**Pros:**

- Single source of truth for status values
- Easier to maintain
- Consistent across codebase

**Cons:**

- Less semantic (can't distinguish between ParityStatus vs ValidationStatus)
- May need separate enums if business logic diverges

#### 3.1.2. Option B: Separate Status Enums (Alternative)

```php
enum ParityStatus: string
{
    case Pass = 'pass';
    case Warning = 'warning';
    case Fail = 'fail';
}

enum ValidationStatus: string
{
    case Pass = 'pass';
    case Warning = 'warning';
    case Fail = 'fail';
}

enum BootstrapStatus: string
{
    case Success = 'success';
    case Warning = 'warning';
    case Failed = 'failed';
}

```

**Pros:**
- More semantic and type-safe
- Allows independent evolution
- Better IDE autocomplete context

**Cons:**
- More enums to maintain
- Potential duplication if values remain identical

**Recommendation: Option B** - Better long-term maintainability and type safety.

### 3.2. Dependency Enums

```php
enum DependencyClassification: string
{
    case Core = 'core';
    case Optional = 'optional';
    case Experimental = 'experimental';
}

enum ReviewCadence: string
{
    case Monthly = 'monthly';
    case Quarterly = 'quarterly';
}

enum RiskLevel: string
{
    case High = 'high';
    case Medium = 'medium';
    case Low = 'low';
}

```

### 3.3. Path Constants

**Recommendation: Keep as constants** - These are configuration values, not domain concepts. Enums would be overkill.

-----

## 4. Proposed Laravel Data Structure

### 4.1. ParityReportData

```php
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;

class ParityReportData extends Data
{
    public function __construct(
        public string $profile,
        public ParityStatus $status,
        /** @var list<string> */
        public array $issues = [],
    ) {}

    public static function from(ParityReport $report): self
    {
        return new self(
            profile: $report->profile,
            status: ParityStatus::from($report->status),
            issues: $report->issues,
        );
    }
}

```

### 4.2. ProfileValidationResultData

```php
class ProfileValidationResultData extends Data
{
    public function __construct(
        public string $profile,
        public ValidationStatus $status,
        /** @var list<string> */
        public array $issues = [],
    ) {}

    public function isPass(): bool
    {
        return $this->status === ValidationStatus::Pass;
    }

    public function isWarning(): bool
    {
        return $this->status === ValidationStatus::Warning;
    }

    public function isFail(): bool
    {
        return $this->status === ValidationStatus::Fail;
    }
}

```

### 4.3. BootstrapRunData

```php
class BootstrapRunData extends Data
{
    public function __construct(
        public string $profile,
        public BootstrapStatus $status,
        public float $durationMinutes,
        /** @var array<string, mixed> */
        public array $notes = [],
    ) {}

    public function isSuccessful(): bool
    {
        return $this->status === BootstrapStatus::Success;
    }

    public function isWarning(): bool
    {
        return $this->status === BootstrapStatus::Warning;
    }
}

```

### 4.4. DependencyRecordData

```php
class DependencyRecordData extends Data
{
    public function __construct(
        public string $name,
        public string $version,
        public DependencyClassification $classification,
        public string $owner,
        public string $justification,
        public Carbon|CarbonImmutable $lastReviewedAt,
        public ReviewCadence $reviewCadence,
        public RiskLevel $riskLevel,
        public string $notes,
    ) {}

    public function reviewDeadline(): Carbon
    {
        $date = $this->lastReviewedAt instanceof Carbon
            ? $this->lastReviewedAt
            : Carbon::parse($this->lastReviewedAt->toDateTimeString());

        return match ($this->reviewCadence) {
            ReviewCadence::Monthly => $date->copy()->addMonthNoOverflow()->endOfDay(),
            ReviewCadence::Quarterly => $date->copy()->addMonthsNoOverflow(3)->endOfDay(),
        };
    }

    public function isOverdue(Carbon|CarbonImmutable $reference): bool
    {
        $carbonRef = $reference instanceof Carbon
            ? $reference
            : Date::parse($reference->toDateTimeString());

        return $this->reviewDeadline()->lessThanOrEqualTo($carbonRef);
    }
}

```

### 4.5. BootstrapRecoveryGuidanceData

```php
class BootstrapRecoveryGuidanceData extends Data
{
    public function __construct(
        public string $title,
        public string $documentation,
        /** @var list<string> */
        public array $nextSteps,
    ) {}
}

```

-----

## 5. Migration Strategy

### 5.1. Phase 1: Create Enums (Low Risk)

1. Create enum files in `app/Enums/`:
   - `ParityStatus.php`
   - `ValidationStatus.php`
   - `BootstrapStatus.php`
   - `DependencyClassification.php`
   - `ReviewCadence.php`
   - `RiskLevel.php`

2. Add helper methods to enums where appropriate (e.g., `label()`, `color()`)

3. **No breaking changes yet** - enums exist alongside constants

### 5.2. Phase 2: Update Data Classes (Medium Risk)

1. Convert readonly classes to Laravel Data:
   - `ParityReport` → `ParityReportData`
   - `ProfileValidationResult` → `ProfileValidationResultData`
   - `BootstrapRun` → `BootstrapRunData`
   - `DependencyRecord` → `DependencyRecordData`
   - `BootstrapRecoveryGuidance` → `BootstrapRecoveryGuidanceData`

2. Update properties to use enums instead of strings

3. Add `from()` static methods for backward compatibility during transition

4. Update all usages:
   - Service classes
   - Console commands
   - Contracts/interfaces

### 5.3. Phase 3: Database Migration (High Risk)

**Critical Consideration:** Database columns storing status values need migration strategy.

1. **Option A: Keep string storage, cast in models**
   - Models cast enum to/from string automatically
   - No database migration needed
   - **Recommended for initial migration**

2. **Option B: Migrate database columns**
   - Add database migration to ensure data integrity
   - More robust long-term
   - Requires data validation

**Files requiring database consideration:**

- `app/Models/ParityResult.php` - has `status` column
- Any other models storing status values

### 5.4. Phase 4: Remove Constants (Low Risk)

1. Remove all constant definitions
2. Update any remaining references
3. Run full test suite

### 5.5. Phase 5: Cleanup & Documentation

1. Update PHPDoc comments
2. Update API documentation if applicable
3. Add migration notes to changelog

-----

## 6. Detailed File Changes

### 6.1. Files to Create

1. `app/Enums/ParityStatus.php`
2. `app/Enums/ValidationStatus.php`
3. `app/Enums/BootstrapStatus.php`
4. `app/Enums/DependencyClassification.php`
5. `app/Enums/ReviewCadence.php`
6. `app/Enums/RiskLevel.php`
7. `app/Data/ParityReportData.php`
8. `app/Data/ProfileValidationResultData.php`
9. `app/Data/BootstrapRunData.php`
10. `app/Data/DependencyRecordData.php`
11. `app/Data/BootstrapRecoveryGuidanceData.php`

### 6.2. Files to Modify

1. `app/Services/BasePlatform/ParityReport.php` - Convert to use enum, consider deprecation
2. `app/Services/BasePlatform/ProfileValidationResult.php` - Convert to use enum, consider deprecation
3. `app/Services/BasePlatform/BootstrapRun.php` - Convert to use enum, consider deprecation
4. `app/Services/BasePlatform/DependencyCatalogue.php` - Update to use enums
5. `app/Services/BasePlatform/DependencyRecord.php` - Convert to Data class
6. `app/Services/BasePlatform/BootstrapRecoveryGuidance.php` - Convert to Data class
7. `app/Services/BasePlatform/ParityChecker.php` - Update to use new Data classes
8. `app/Services/BasePlatform/EnvironmentProfileValidator.php` - Update to use new Data classes
9. `app/Services/BasePlatform/BootstrapRunner.php` - Update to use new Data classes
10. `app/Console/Commands/RunParityCheck.php` - Update to use enums
11. `app/Console/Commands/ValidateEnvironmentProfiles.php` - Update to use enums
12. `app/Console/Commands/RunPlatformBootstrap.php` - Update to use enums
13. `app/Console/Commands/DependencyReviewReport.php` - Update to use enums
14. `app/Contracts/BasePlatform/ParityCheckerContract.php` - Update return types
15. `app/Contracts/BasePlatform/EnvironmentProfileValidatorContract.php` - Update return types
16. `app/Contracts/BasePlatform/BootstrapRunnerContract.php` - Update return types
17. `app/Models/ParityResult.php` - Add enum casting (if exists)

-----

## 7. Pros and Cons

### 7.1. Pros

#### 7.1.1. Type Safety (High Impact)

- **Compile-time validation**: Enums prevent invalid values at the type level
- **IDE support**: Better autocomplete and refactoring
- **Static analysis**: PHPStan/Larastan can catch enum misuse
- **Reduced bugs**: Impossible to pass wrong status string

#### 7.1.2. Laravel Data Benefits (High Impact)

- **Automatic serialization**: JSON, arrays, requests handled automatically
- **Validation**: Built-in validation rules
- **Transformations**: Easy data transformation between formats
- **Type casting**: Automatic casting of nested data objects
- **API Resources**: Can be used directly as API resources
- **Immutability**: Data objects are immutable by default

#### 7.1.3. Code Quality (Medium Impact)

- **Self-documenting**: Enums make valid values explicit
- **Refactoring**: Easier to rename/change values across codebase
- **Testing**: Easier to test with enum cases
- **Consistency**: Single source of truth for domain values

#### 7.1.4. Maintainability (Medium Impact)

- **Less magic strings**: No more string literals scattered in code
- **Centralized logic**: Enum methods can contain domain logic
- **Version control**: Changes to valid values are tracked in one place

### 7.2. Cons

#### 7.2.1. Migration Complexity (High Impact)

- **Breaking changes**: Requires coordinated updates across multiple files
- **Database compatibility**: Need to handle enum serialization in database
- **API compatibility**: External APIs may expect strings
- **Testing overhead**: Need to update all tests

#### 7.2.2. Learning Curve (Low Impact)

- **Team familiarity**: Team needs to understand enum patterns
- **Laravel Data**: Team needs to learn Laravel Data features
- **Best practices**: Need to establish patterns for enum usage

#### 7.2.3. Performance (Negligible)

- **Minimal overhead**: Enums are lightweight
- **Serialization**: Laravel Data adds slight overhead for serialization
- **Not a concern**: Performance impact is negligible in practice

#### 7.2.4. Potential Issues (Medium Impact)

- **Database storage**: Need to ensure enum values serialize correctly
- **JSON APIs**: Need to ensure enums serialize to strings in JSON
- **Legacy code**: May have hardcoded string comparisons elsewhere

-----

## 8. Risk Assessment

### 8.1. Low Risk

- Creating enums (additive change)
- Creating Data classes (additive change)
- Adding helper methods to enums

### 8.2. Medium Risk

- Updating service classes to use enums
- Updating console commands
- Updating contracts/interfaces

### 8.3. High Risk

- Database column changes (if needed)
- Removing old constants (breaking change)
- API compatibility (if external consumers exist)

-----

## 9. Testing Strategy

### 9.1. Unit Tests

- Test enum creation and value access
- Test enum methods (if any)
- Test Data class creation from arrays
- Test Data class serialization to arrays/JSON
- Test Data class validation

### 9.2. Integration Tests

- Test service methods return correct Data objects
- Test console commands handle enums correctly
- Test database persistence with enum values
- Test API responses serialize correctly

### 9.3. Regression Tests

- Ensure all existing functionality works
- Test edge cases (invalid enum values, null handling)
- Test backward compatibility during transition

-----

## 10. Recommendation

### 10.1. Overall Recommendation: **85% - Proceed with Phased Migration**

**Rationale:**

- Strong type safety improvements
- Better developer experience
- Modern PHP 8.1+ patterns
- Laravel Data provides significant value
- Risks are manageable with phased approach

### 10.2. Recommended Approach

1. **Start with Phase 1 (Enums)** - Low risk, high value
2. **Proceed to Phase 2 (Data Classes)** - Medium risk, high value
3. **Carefully evaluate Phase 3 (Database)** - Only if needed
4. **Complete Phase 4 (Cleanup)** - After thorough testing
5. **Document in Phase 5** - For future reference

### 10.3. Alternative: Hybrid Approach (90% Recommendation)

**Consider keeping old classes as deprecated wrappers** during transition:

- Create new Data classes
- Keep old readonly classes that delegate to Data classes
- Mark old classes as `@deprecated`
- Migrate gradually
- Remove old classes in next major version

This provides:

- Zero breaking changes initially
- Gradual migration path
- Time to validate approach
- Easy rollback if issues arise

-----

## 11. Implementation Checklist

### 11.1. Pre-Implementation

- [ ] Review this plan with team
- [ ] Confirm database migration strategy
- [ ] Check for external API consumers
- [ ] Review test coverage requirements

### 11.2. Phase 1: Enums

- [ ] Create all enum files
- [ ] Add enum helper methods
- [ ] Write tests for enums
- [ ] Update documentation

### 11.3. Phase 2: Data Classes

- [ ] Create all Data class files
- [ ] Add `from()` methods for compatibility
- [ ] Update service classes
- [ ] Update console commands
- [ ] Update contracts
- [ ] Write tests

### 11.4. Phase 3: Database (if needed)

- [ ] Review model casting requirements
- [ ] Create migrations if needed
- [ ] Test database persistence
- [ ] Test data migration

### 11.5. Phase 4: Cleanup

- [ ] Remove constant definitions
- [ ] Remove deprecated classes (if hybrid approach)
- [ ] Update all references
- [ ] Run full test suite

### 11.6. Phase 5: Documentation

- [ ] Update code comments
- [ ] Update API docs
- [ ] Add migration notes
- [ ] Update team documentation

-----

## 12. Questions for Clarification

1. **Database Strategy**: Should we keep string storage with model casting, or migrate to enum columns?
2. **API Compatibility**: Are there external API consumers that expect string values?
3. **Migration Timeline**: Is this a breaking change for a major version, or can we do it incrementally?
4. **Enum Naming**: Do we prefer separate enums (ParityStatus, ValidationStatus) or shared (Status)?
5. **Backward Compatibility**: Should we maintain old classes as deprecated wrappers during transition?

-----

## 13. References

- [PHP 8.1 Enums Documentation](https://www.php.net/manual/en/language.types.enumerations.php)
- [Spatie Laravel Data Documentation](https://spatie.be/docs/laravel-data)
- [Laravel Enum Casting](https://laravel.com/docs/eloquent-mutators#enum-casting)
- Existing enum examples: `app/Enums/Theme.php`, `app/Enums/ThemeAccent.php`, `app/Enums/ThemeFlavor.php`
- Existing Data example: `app/Data/UserSettingsData.php`

-----

**Document Version**: 1.0
**Last Updated**: 2025-01-27
**Author**: AI Assistant
**Status**: Draft - Awaiting Review
