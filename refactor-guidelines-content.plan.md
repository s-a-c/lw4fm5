<!-- 94fa1f95-17b1-4309-b58d-284452082568 938c8bf3-3d56-410c-8b41-0e0cb9f5a52a -->
# Refactor Webkul References to AureusERP Guidelines

## Overview

All `webkul` references in the AI Guidelines are specific to the `aureauserp/aureuserp` composer package (Laravel + FilamentPHP). These should be collated and refactored into a dedicated section under PHP-Laravel, clarifying the package context while maintaining the `Webkul\` namespace used by the actual package.

## Current State Analysis

Found 25 references to `webkul` across:

- `010-project-overview.md` - Plugin path structure (`/plugins/webkul/`)
- `021-testing-examples.md` - Multiple namespace examples (`Webkul\Product`, `Webkul\Security`)
- Test templates - Namespace examples in all three template files

## Implementation Plan

### 1. Create AureusERP Directory Structure

- Create `.ai/AI-GUIDELINES/PHP-Laravel/AureusERP/` directory
- Create `000-index.md` as the main entry point
- Structure will mirror PHP-Laravel organization patterns

### 2. Create Package-Specific Documentation Files

#### 2.1. Package Overview (`010-package-overview.md`)

- Extract and enhance plugin architecture section from `010-project-overview.md`
- Document that `aureauserp/aureuserp` package uses `Webkul\` namespace
- Clarify plugin path structure (verify if `/plugins/webkul/` or `/plugins/aureauserp/` based on actual package)
- Document FilamentPHP integration specifics
- List all business plugins (Products, Security, etc.)

#### 2.2. Testing Examples (`020-testing-examples.md`)

- Move all `Webkul\` namespace examples from `021-testing-examples.md` to this dedicated file
- Add context that these examples are for `aureauserp/aureuserp` (AureusERP) package
- Maintain all existing examples (Product, Security, etc.)
- Cross-reference back to generic testing standards

#### 2.3. Test Templates (`030-templates/`)

- Create `030-templates/` subdirectory
- Move/copy test templates from `PHP-Laravel/templates/`
- Update templates to clarify they're for `aureauserp/aureuserp` (AureusERP) package
- Update namespace comments to reference `Webkul\` namespace

### 3. Update Existing Files

#### 3.1. Update `010-project-overview.md`

- Remove or generalize plugin architecture section (lines 27-43)
- Replace with reference to AureusERP package overview
- Keep generic project structure information
- Add cross-reference to AureusERP section

#### 3.2. Update `021-testing-examples.md`

- Remove all `Webkul\` namespace examples
- Replace with generic examples or placeholders
- Add cross-reference to AureusERP testing examples
- Maintain structure but with generic namespaces

#### 3.3. Update `000-index.md`

- Add new section for AureusERP guidelines
- Include in quick reference and navigation
- Update integration sections

### 4. Verify Plugin Path Structure

- Check if actual package uses `/plugins/webkul/` or `/plugins/aureauserp/`
- Update path references accordingly
- Document the path structure in package overview

### 5. Update Cross-References

- Update all navigation footers
- Update index files
- Ensure all links point to correct locations
- Maintain documentation standards (H1 format, navigation format)

## File Changes Summary

### New Files

- `.ai/AI-GUIDELINES/PHP-Laravel/AureusERP/000-index.md`
- `.ai/AI-GUIDELINES/PHP-Laravel/AureusERP/010-package-overview.md`
- `.ai/AI-GUIDELINES/PHP-Laravel/AureusERP/020-testing-examples.md`
- `.ai/AI-GUIDELINES/PHP-Laravel/AureusERP/030-templates/000-index.md`
- `.ai/AI-GUIDELINES/PHP-Laravel/AureusERP/030-templates/integration-test-template.php`
- `.ai/AI-GUIDELINES/PHP-Laravel/AureusERP/030-templates/feature-test-template.php`
- `.ai/AI-GUIDELINES/PHP-Laravel/AureusERP/030-templates/unit-test-template.php`

### Modified Files

- `.ai/AI-GUIDELINES/PHP-Laravel/000-index.md` - Add AureusERP section
- `.ai/AI-GUIDELINES/PHP-Laravel/010-project-overview.md` - Remove/generalize plugin section
- `.ai/AI-GUIDELINES/PHP-Laravel/021-testing-examples.md` - Remove Webkul examples, add generic ones

## Notes

- Keep `Webkul\` namespace in all examples (this is what the package uses)
- Verify plugin path structure from actual package/repository
- Maintain all existing documentation standards (no H1 anchors, proper navigation format)
- Ensure code blocks have language tags

## Status: ✅ COMPLETED

All tasks have been successfully completed. The refactoring is complete with all Webkul references properly organized into the AureusERP section, maintaining the `Webkul\` namespace convention, and all cross-references and navigation footers updated.

### Implementation Summary

✅ **Created AureusERP directory structure** - Complete with index file
✅ **Created package overview document** - Extracted and enhanced plugin architecture section
✅ **Moved Webkul testing examples** - All examples now in dedicated AureusERP testing examples file
✅ **Created test templates directory** - All three templates copied and updated with AureusERP context
✅ **Updated project overview** - Plugin section generalized with cross-references to AureusERP
✅ **Updated generic testing examples** - Removed Webkul examples, added generic examples with cross-references
✅ **Updated PHP-Laravel index** - Added AureusERP section with proper integration
✅ **Verified plugin path structure** - Documented in package overview with note about path variability
✅ **Updated all navigation footers** - All cross-references and navigation footers are correct

### To-dos

- [x] Create AureusERP directory structure and index file
- [x] Create package overview document extracting plugin architecture from project overview
- [x] Move Webkul testing examples to dedicated AureusERP testing examples file
- [x] Create AureusERP test templates directory and move/copy templates
- [x] Update project overview to remove/generalize plugin section and add cross-reference
- [x] Remove Webkul examples from generic testing examples and add cross-reference
- [x] Update PHP-Laravel index to include AureusERP section
- [x] Verify and update plugin path structure references
- [x] Update all navigation footers and cross-references throughout documentation
