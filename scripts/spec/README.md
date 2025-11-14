# Spec-Kit Helper Scripts

Helper scripts for streamlining the specification-driven development workflow with spec-kit.

## Overview

These scripts automate the spec-to-code workflow, handling branch creation, AI prompt preparation, and artifact committing. They integrate seamlessly with both Git and Jujutsu (jj).

## Installation

Source the aliases file in your shell configuration:

```bash
# Add to ~/.zshrc or ~/.bashrc
source /path/to/project/scripts/spec/aliases.sh
```

Or use scripts directly:

```bash
./scripts/spec/start-spec.sh 001-feature-name "Description"
```

## Scripts

### `start-spec.sh`

Creates a new specification branch and directory structure.

**Usage**:
```bash
./scripts/spec/start-spec.sh <spec-identifier> [description]
```

**Example**:
```bash
./scripts/spec/start-spec.sh 001-user-auth "User authentication system"
```

**What it does**:
- Creates spec branch: `spec/001-user-auth`
- Creates spec directory: `specs/001-user-auth/`
- Creates template `spec.md` file
- Initializes directory structure (checklists/, contracts/)
- Commits initial spec template

**Requirements**:
- Must be run from `develop` branch
- Spec identifier must match format: `###-feature-name` (e.g., `001-user-auth`)

---

### `generate-plan.sh`

Prepares context for `/speckit.plan` command and handles generated `plan.md`.

**Usage**:
```bash
./scripts/spec/generate-plan.sh <spec-identifier>
```

**Example**:
```bash
./scripts/spec/generate-plan.sh 001-user-auth
```

**What it does**:
- Validates `spec.md` exists
- Prints AI prompt with spec file location
- Waits for user to execute `/speckit.plan` in AI agent
- Detects generated `plan.md`
- Commits generated plan

**Requirements**:
- `spec.md` must exist
- User must execute `/speckit.plan` in AI agent after script runs

**AI Prompt Output**:
```
Use '/speckit.plan' with spec at:
  `/path/to/project/specs/001-user-auth/spec.md`
```

---

### `generate-tasks.sh`

Prepares context for `/speckit.tasks` command and handles generated `tasks.md`.

**Usage**:
```bash
./scripts/spec/generate-tasks.sh <spec-identifier>
```

**Example**:
```bash
./scripts/spec/generate-tasks.sh 001-user-auth
```

**What it does**:
- Validates `plan.md` exists (requires plan to be generated first)
- Prints AI prompt with plan file location
- Waits for user to execute `/speckit.tasks` in AI agent
- Detects generated `tasks.md`
- Commits generated tasks

**Requirements**:
- `plan.md` must exist (run `generate-plan.sh` first)
- User must execute `/speckit.tasks` in AI agent after script runs

**AI Prompt Output**:
```
Use '/speckit.tasks' with plan at:
  `/path/to/project/specs/001-user-auth/plan.md`
```

---

### `generate-clarify.sh`

Prepares context for `/speckit.clarify` command.

**Usage**:
```bash
./scripts/spec/generate-clarify.sh <spec-identifier>
```

**Example**:
```bash
./scripts/spec/generate-clarify.sh 001-user-auth
```

**What it does**:
- Validates `spec.md` exists
- Prints AI prompt with spec file location

**Note**: This script only prints the AI prompt. It does not wait for or commit generated output (clarifications may be integrated into spec.md directly).

---

### `generate-checklist.sh`

Prepares context for `/speckit.checklist` command.

**Usage**:
```bash
./scripts/spec/generate-checklist.sh <spec-identifier> [spec|plan]
```

**Example**:
```bash
# Generate checklists for specification
./scripts/spec/generate-checklist.sh 001-user-auth spec

# Generate checklists for plan
./scripts/spec/generate-checklist.sh 001-user-auth plan
```

**What it does**:
- Validates required file exists (spec.md or plan.md)
- Prints AI prompt with appropriate file location
- Generates checklists in `checklists/spec/` or `checklists/plan/`

**Note**: This script only prints the AI prompt. Commit generated checklists manually.

---

### `finish-spec.sh`

Validates spec completeness and merges spec branch to `develop`.

**Usage**:
```bash
./scripts/spec/finish-spec.sh <spec-identifier> [--pr]
```

**Example**:
```bash
# Merge spec branch directly
./scripts/spec/finish-spec.sh 001-user-auth

# Open PR instead of merging
./scripts/spec/finish-spec.sh 001-user-auth --pr
```

**What it does**:
- Validates required files exist (spec.md, plan.md, tasks.md)
- Checks out `develop` and updates from remote
- Merges spec branch to `develop`
- Or opens PR if `--pr` flag is used

**Requirements**:
- Must be on spec branch or willing to switch
- All required artifacts must exist (spec.md, plan.md, tasks.md)

---

### `start-feature.sh`

Creates feature branch from spec, referencing spec in commit message.

**Usage**:
```bash
./scripts/spec/start-feature.sh <spec-identifier>
```

**Example**:
```bash
./scripts/spec/start-feature.sh 001-user-auth
```

**What it does**:
- Validates spec directory exists
- Creates feature branch: `feature/001-user-auth`
- References spec branch/commit in commit message
- Prints spec file locations for reference

**Requirements**:
- Must be run from `develop` branch
- Spec must exist (spec directory must be present)

**Output**:
- Feature branch created
- Spec file locations printed for reference during implementation

---

## Workflow Example

Complete workflow from spec to feature:

```bash
# 1. Start new specification
./scripts/spec/start-spec.sh 001-user-auth "User authentication system"

# 2. Edit spec.md with feature requirements
# ... edit specs/001-user-auth/spec.md ...

# 3. Generate plan (prints AI prompt, wait for AI to generate plan.md)
./scripts/spec/generate-plan.sh 001-user-auth
# Execute '/speckit.plan' in AI agent with printed prompt
# ... AI generates plan.md ...

# 4. Generate tasks (prints AI prompt, wait for AI to generate tasks.md)
./scripts/spec/generate-tasks.sh 001-user-auth
# Execute '/speckit.tasks' in AI agent with printed prompt
# ... AI generates tasks.md ...

# 5. Generate checklists (optional)
./scripts/spec/generate-checklist.sh 001-user-auth spec
# Execute '/speckit.checklist' in AI agent
# ... AI generates checklists ...

# 6. Review and finalize spec
# ... review all artifacts ...

# 7. Finish spec (merge to develop)
./scripts/spec/finish-spec.sh 001-user-auth

# 8. Create feature branch from spec
./scripts/spec/start-feature.sh 001-user-auth

# 9. Implement feature following tasks.md
# ... implement feature ...
```

## Shell Aliases

Source `aliases.sh` for convenient aliases:

```bash
source scripts/spec/aliases.sh

# Use aliases
spec-start 001-user-auth "Description"
spec-plan 001-user-auth
spec-tasks 001-user-auth
spec-finish 001-user-auth
spec-feature 001-user-auth
```

## Integration with Jujutsu (jj)

All scripts automatically detect and use `jj` if available:

- Uses `jj branch create` instead of `git checkout -b`
- Uses `jj new` instead of `git commit`
- Uses `jj git push` for syncing
- Falls back to git commands if jj not available

## Error Handling

Scripts include comprehensive error handling:

- **Validation**: Checks spec identifier format, file existence, branch status
- **Helpful Messages**: Clear error messages with next steps
- **User Confirmation**: Prompts before destructive operations
- **Color Output**: Color-coded messages for clarity (info, success, error, warning)

## Troubleshooting

**Issue**: Script fails with "Invalid spec identifier format"
**Solution**: Ensure spec identifier matches format `###-feature-name` (e.g., `001-user-auth`)

**Issue**: Script fails with "Spec directory not found"
**Solution**: Run `start-spec.sh` first to create the spec directory

**Issue**: Script fails with "Not on develop branch"
**Solution**: Switch to develop branch: `git checkout develop`

**Issue**: Script fails with "plan.md not found"
**Solution**: Run `generate-plan.sh` first to generate plan.md

**Issue**: AI-generated files not detected
**Solution**: Ensure files are saved in the correct spec directory. Check file paths match what script expects.

## Best Practices

1. **Use numeric prefixes**: Match spec directory structure (e.g., `001-`, `002-`)
2. **Review before finishing**: Validate all artifacts before running `finish-spec.sh`
3. **Commit artifacts separately**: Each generated artifact should be committed separately
4. **Reference specs in features**: Link feature branches to their specifications
5. **Keep specs up to date**: Update specs and regenerate artifacts as requirements change

## Related Documentation

- [Spec-Driven Development Workflow](../docs/010-setup/125-spec-driven-development.md)
- [Development Tools Documentation](../docs/010-setup/120-development-tools.md)
- [Spec-Kit Documentation](https://spec-kit.org)
