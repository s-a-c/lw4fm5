# Spec-Driven Development Workflow

Compliant with [AI-GUIDELINES.md](../../.ai/AI-GUIDELINES.md) v0921d4cfab198af1451ef177b6e47657b5d3ab0292f77bf232496291dee47183
<!-- markdownlint-disable MD013 -->

<details>
<summary>Expand for Table of Contents</summary>

- [Spec-Driven Development Workflow](#spec-driven-development-workflow)
  - [1 Introduction](#1-introduction)
    - [1.1 What is Spec-Driven Development?](#11-what-is-spec-driven-development)
    - [1.2 Why Spec-Kit?](#12-why-spec-kit)
  - [2 Spec-Kit Overview](#2-spec-kit-overview)
    - [2.1 Installation](#21-installation)
    - [2.2 Key Commands](#22-key-commands)
    - [2.3 How It Works](#23-how-it-works)
  - [3 Workflow](#3-workflow)
    - [3.1 Branch Strategy](#31-branch-strategy)
    - [3.2 Step-by-Step Workflow](#32-step-by-step-workflow)
    - [3.3 Helper Scripts](#33-helper-scripts)
  - [4 Artifact Structure](#4-artifact-structure)
    - [4.1 Specification File (spec.md)](#41-specification-file-specmd)
    - [4.2 Plan File (plan.md)](#42-plan-file-planmd)
    - [4.3 Tasks File (tasks.md)](#43-tasks-file-tasksmd)
    - [4.4 Checklists Directory](#44-checklists-directory)
    - [4.5 Contracts Directory](#45-contracts-directory)
  - [5 Integration with Git Flow and Jujutsu](#5-integration-with-git-flow-and-jujutsu)
    - [5.1 Spec Branches](#51-spec-branches)
    - [5.2 Feature Branches](#52-feature-branches)
    - [5.3 Merging Strategy](#53-merging-strategy)
  - [6 Best Practices](#6-best-practices)
    - [6.1 Writing Specifications](#61-writing-specifications)
    - [6.2 Generating Plans and Tasks](#62-generating-plans-and-tasks)
    - [6.3 Reviewing and Finalizing](#63-reviewing-and-finalizing)
    - [6.4 Implementing Features](#64-implementing-features)
  - [7 Troubleshooting](#7-troubleshooting)
  - [8 Next Steps](#8-next-steps)
  - [9 Navigation](#9-navigation)

</details>

---

## 1 Introduction

This project uses **Specification-Driven Development (SDD)** with **Spec-Kit** to ensure features are well-specified, planned, and tracked before implementation begins.

### 1.1 What is Spec-Driven Development?

Spec-Driven Development is a methodology where:

1. **Specifications come first**: Features are specified in detail before any code is written
2. **AI-powered planning**: Specifications are transformed into technical plans using AI
3. **Task breakdown**: Plans are broken down into actionable tasks
4. **Quality gates**: Checklists ensure completeness, security, and performance
5. **Clear separation**: Specifications live separately from code implementation

This approach ensures that requirements are clear, architecture is thought through, and implementation follows a well-defined plan.

### 1.2 Why Spec-Kit?

Spec-Kit provides:

- **AI-powered planning**: Generates technical plans from specifications
- **Task breakdown**: Creates actionable tasks from plans
- **Quality checklists**: Ensures coverage of security, performance, and architecture concerns
- **Workflow integration**: Works seamlessly with Git Flow and Jujutsu
- **Automation**: Helper scripts streamline the spec-to-code workflow

## 2 Spec-Kit Overview

### 2.1 Installation

Spec-Kit is installed via `uvx`:

```bash
# Install spec-kit globally (one-time setup)
uvx --from git+https://github.com/github/spec-kit.git specify init

# Verify installation
specify --version
```

**Prerequisites**:

- Python 3.11+
- Git
- An AI coding agent (Cursor, Claude Desktop, etc.)

### 2.2 Key Commands

The `/speckit` commands are **AI agent prompts**, not CLI commands. They are used within AI coding assistants:

- **`/speckit.plan`** - Generates technical implementation plan from specification
- **`/speckit.tasks`** - Generates actionable tasks from plan
- **`/speckit.clarify`** - Generates clarifications for ambiguous requirements
- **`/speckit.checklist`** - Generates quality checklists (architecture, security, performance, etc.)

### 2.3 How It Works

1. **Write Specification**: Create `specs/###-feature-name/spec.md` with feature requirements
2. **Use AI Agent**: Execute `/speckit.plan` in your AI coding assistant (Cursor, Claude Desktop)
3. **AI Generates Plan**: AI reads spec and generates `plan.md` with technical design
4. **Generate Tasks**: Execute `/speckit.tasks` to break plan into actionable tasks
5. **Review Checklists**: Execute `/speckit.checklist` to generate quality assurance checklists
6. **Commit Artifacts**: All generated files are committed to the repository

**Important**: The `/speckit` commands are prompts for AI agents, not shell commands. Helper scripts (see [Helper Scripts](#33-helper-scripts)) automate context preparation and artifact handling.

## 3 Workflow

### 3.1 Branch Strategy

This project uses **separate branches for specifications and code**:

- **Spec Branches**: `spec/###-feature-name` (e.g., `spec/001-base-platform`)
- **Feature Branches**: `feature/###-feature-name` (e.g., `feature/001-base-platform`)

**Benefits**:

- Clear separation of concerns
- Specs can be reviewed independently
- Code branches reference their specifications
- Easier to track spec-to-code relationship

### 3.2 Step-by-Step Workflow

1. **Create Spec Branch**
   ```bash
   ./scripts/spec/start-spec.sh 001-feature-name "Feature description"
   # Or manually:
   git flow feature start spec/001-feature-name
   ```

2. **Write Specification**
   - Edit `specs/001-feature-name/spec.md`
   - Include user stories, acceptance criteria, constraints
   - Reference existing specs for patterns

3. **Generate Plan**
   ```bash
   ./scripts/spec/generate-plan.sh 001-feature-name
   # Script prints AI prompt: "Use '/speckit.plan' with spec at 'specs/001-feature-name/spec.md'"
   # Execute in AI agent, AI generates plan.md
   ```

4. **Generate Tasks**
   ```bash
   ./scripts/spec/generate-tasks.sh 001-feature-name
   # Script prints AI prompt: "Use '/speckit.tasks' with plan at 'specs/001-feature-name/plan.md'"
   # Execute in AI agent, AI generates tasks.md
   ```

5. **Generate Checklists** (Optional)
   ```bash
   ./scripts/spec/generate-checklist.sh 001-feature-name spec
   # Or for plan checklists:
   ./scripts/spec/generate-checklist.sh 001-feature-name plan
   ```

6. **Review and Finalize**
   - Review all artifacts (spec.md, plan.md, tasks.md, checklists/)
   - Ensure completeness and accuracy
   - Update spec if needed, regenerate plan/tasks

7. **Finish Spec**
   ```bash
   ./scripts/spec/finish-spec.sh 001-feature-name
   # Validates completeness and merges spec branch to develop
   ```

8. **Create Feature Branch**
   ```bash
   ./scripts/spec/start-feature.sh 001-feature-name
   # Creates feature branch from spec, references spec in commit message
   ```

9. **Implement Feature**
   - Follow tasks.md for implementation steps
   - Reference spec and plan during development
   - Check off tasks as you complete them

### 3.3 Helper Scripts

Helper scripts automate the spec-to-code workflow. See `scripts/spec/README.md` for detailed documentation.

**Available Scripts**:

- `start-spec.sh` - Create new spec branch and directory structure
- `generate-plan.sh` - Prepare context for `/speckit.plan` command
- `generate-tasks.sh` - Prepare context for `/speckit.tasks` command
- `generate-clarify.sh` - Prepare context for `/speckit.clarify` command
- `generate-checklist.sh` - Prepare context for `/speckit.checklist` command
- `finish-spec.sh` - Validate and merge spec branch
- `start-feature.sh` - Create feature branch from spec

**Shell Aliases**: Source `scripts/spec/aliases.sh` for convenient aliases:

```bash
source scripts/spec/aliases.sh
spec-start 001-feature-name "Description"
spec-plan 001-feature-name
spec-tasks 001-feature-name
spec-finish 001-feature-name
spec-feature 001-feature-name
```

## 4 Artifact Structure

Each specification creates the following structure:

```text
specs/###-feature-name/
├── spec.md              # Feature specification (written by developer)
├── plan.md              # Technical implementation plan (generated by AI)
├── tasks.md             # Actionable tasks (generated by AI)
├── research.md          # Phase 0 research (generated by AI)
├── data-model.md        # Phase 1 data model (generated by AI)
├── quickstart.md        # Phase 1 quickstart guide (generated by AI)
├── checklists/          # Quality assurance checklists (generated by AI)
│   ├── spec/            # Checklists for specification quality
│   └── plan/            # Checklists for plan quality
└── contracts/           # API and interaction contracts (generated by AI)
```

### 4.1 Specification File (spec.md)

The specification file defines:

- **User Stories**: Feature requirements from user perspective
- **Acceptance Criteria**: How to verify the feature works
- **Constraints**: Technical or business limitations
- **Success Criteria**: Measurable outcomes
- **Out of Scope**: Explicitly excluded items

**Example Structure**:

```markdown
# Feature Specification: Feature Name

**Feature Branch**: `001-feature-name`
**Created**: YYYY-MM-DD
**Status**: Draft

## User Scenarios & Testing

### User Story 1 - Feature Description (Priority: P1)

Description of user story...

**Acceptance Scenarios**:
1. Given... When... Then...
2. Given... When... Then...
```

### 4.2 Plan File (plan.md)

Generated by `/speckit.plan`, contains:

- **Summary**: High-level overview
- **Technical Context**: Language versions, dependencies, constraints
- **Constitution Check**: Validates spec against project standards
- **Project Structure**: File and directory organization
- **Phase Breakdown**: Implementation phases (Research, Design, Implementation, Testing)
- **Operational Design**: Deployment, monitoring, scaling considerations

### 4.3 Tasks File (tasks.md)

Generated by `/speckit.tasks`, contains:

- **Phase-by-phase tasks**: Organized by implementation phase
- **Task dependencies**: Which tasks must complete before others
- **Acceptance criteria**: How to verify each task
- **Estimated complexity**: Time and difficulty estimates

### 4.4 Checklists Directory

Generated by `/speckit.checklist`, contains quality assurance checklists:

- **Architecture**: System design and patterns
- **Security**: Authentication, authorization, data protection
- **Performance**: SLAs, optimization, caching
- **Observability**: Logging, monitoring, alerting
- **Data Integrity**: Validation, consistency, backups
- **Test Plan**: Coverage, test types, automation
- **Requirements**: Specification completeness

### 4.5 Contracts Directory

Generated during planning, contains:

- **API Contracts**: Request/response schemas
- **Event Contracts**: Event payloads and handlers
- **Service Contracts**: Service boundaries and interfaces

## 5 Integration with Git Flow and Jujutsu

### 5.1 Spec Branches

Spec branches follow the pattern `spec/###-feature-name`:

```bash
# Create spec branch using Git Flow
git flow feature start spec/001-feature-name

# Or using jj
jj branch create spec/001-feature-name
```

**Workflow**:

1. Create spec branch from `develop`
2. Write specification and generate artifacts
3. Review and iterate on spec
4. Merge spec branch to `develop` when finalized
5. Spec artifacts remain in `develop` for reference

### 5.2 Feature Branches

Feature branches follow the pattern `feature/###-feature-name`:

```bash
# Create feature branch from spec
./scripts/spec/start-feature.sh 001-feature-name

# Or manually
git flow feature start 001-feature-name
```

**Workflow**:

1. Create feature branch from `develop` (which includes spec)
2. Reference spec branch/commit in feature commits
3. Implement according to plan.md and tasks.md
4. Merge feature branch to `develop` when complete

### 5.3 Merging Strategy

**Spec Merging**:

```bash
# Finish spec branch
./scripts/spec/finish-spec.sh 001-feature-name

# Or manually with Git Flow
git flow feature finish spec/001-feature-name
# Merges spec branch to develop, keeps spec artifacts
```

**Feature Merging**:

```bash
# Finish feature branch
git flow feature finish 001-feature-name
# Merges feature branch to develop, includes implementation
```

**Jujutsu Integration**:

```bash
# Use jj for daily development
jj new -m "Implement task T001"
# ... work on implementation ...
jj git push  # Sync to git branch

# Finish feature (uses git flow)
git flow feature finish 001-feature-name
```

## 6 Best Practices

### 6.1 Writing Specifications

1. **Start with user stories**: Focus on user needs, not implementation
2. **Be specific**: Include acceptance criteria and test scenarios
3. **Define constraints**: Technical limitations, performance requirements
4. **Reference existing specs**: Reuse patterns from successful features
5. **Keep focused**: One feature per specification

### 6.2 Generating Plans and Tasks

1. **Generate plan first**: Always generate plan before tasks
2. **Review plan thoroughly**: Ensure technical approach aligns with spec
3. **Generate checklists**: Use checklists to validate completeness
4. **Iterate on spec**: Update spec and regenerate if plan reveals issues
5. **Commit artifacts**: Commit each generated artifact separately

### 6.3 Reviewing and Finalizing

1. **Review all artifacts**: Check spec, plan, tasks, and checklists
2. **Validate completeness**: Ensure all acceptance criteria are covered
3. **Check consistency**: Plan should align with spec, tasks with plan
4. **Update if needed**: Revise spec and regenerate artifacts as needed
5. **Get approval**: Have spec reviewed before merging to develop

### 6.4 Implementing Features

1. **Follow tasks.md**: Use generated tasks as implementation guide
2. **Reference spec and plan**: Keep spec and plan visible during development
3. **Check off tasks**: Mark tasks as complete in tasks.md
4. **Use checklists**: Validate against quality checklists
5. **Link commits**: Reference spec branch/commit in feature commits

## 7 Troubleshooting

**Issue**: AI agent doesn't recognize `/speckit` command
**Solution**: Ensure you're using a compatible AI agent (Cursor, Claude Desktop). Check that spec-kit is properly initialized.

**Issue**: Generated plan.md doesn't match spec
**Solution**: Review spec for clarity and completeness. Regenerate plan with updated spec. Use `/speckit.clarify` for ambiguous requirements.

**Issue**: Tasks are too vague or too detailed
**Solution**: Regenerate tasks after refining plan. Ensure plan has sufficient detail for task generation.

**Issue**: Helper scripts fail
**Solution**: Check that spec directory exists and follows naming convention (`###-feature-name`). Verify git/jj is initialized.

**Issue**: Spec branch conflicts with feature branch
**Solution**: Specs and features use different branch prefixes (`spec/` vs `feature/`). Ensure numeric prefix matches in both.

## 8 Next Steps

- [Development Tools →](120-development-tools.md) - View complete development tools documentation
- [Helper Scripts Documentation →](../scripts/spec/README.md) - Detailed script usage
- [Project Overview →](020-overview.md) - Understand project architecture

---

## 9 Navigation

[← Development Tools](120-development-tools.md) | [↑ Top](#spec-driven-development-workflow) | [Frontend Build →](130-frontend-build.md)
