# Git/JJ Workflow Guide

Compliant with [AI-GUIDELINES.md](../../.ai/AI-GUIDELINES.md) v0921d4cfab198af1451ef177b6e47657b5d3ab0292f77bf232496291dee47183

<!-- markdownlint-disable MD013 -->

<details>
<summary>Expand for Table of Contents</summary>

- [Git/JJ Workflow Guide](#gitjj-workflow-guide)
  - [1 Overview](#1-overview)
  - [2 Pre-Commit Verification](#2-pre-commit-verification)
  - [3 Commit with jj](#3-commit-with-jj)
  - [4 Sync with Git Develop Branch](#4-sync-with-git-develop-branch)
  - [5 GitHub Workflow Verification](#5-github-workflow-verification)
  - [6 Create PR to Main](#6-create-pr-to-main)
  - [7 PR Review Process](#7-pr-review-process)
  - [8 Fix Issues Locally](#8-fix-issues-locally)
  - [9 Merge Successful PR](#9-merge-successful-pr)
  - [10 Sync Local Branches](#10-sync-local-branches)
  - [11 Troubleshooting](#11-troubleshooting)

</details>

## 1 Overview

This guide documents the complete workflow for committing changes, syncing with git, creating pull requests, and merging to main. It supports both **jj (Jujutsu)** and **git** workflows, allowing you to use either tool as needed.

### Workflow Summary

1. Verify all checks pass before committing
2. Commit changes with jj using comprehensive messages
3. Sync jj changes to git and push to develop branch
4. Verify GitHub Actions workflows complete successfully
5. Create PR from develop to main
6. Review PR and address any feedback
7. Fix issues locally and push updates (if needed)
8. Merge successful PR into main
9. Sync local main and develop branches
10. Verify everything is in sync and ready for next cycle

## 2 Pre-Commit Verification

Before committing, ensure all quality checks pass and verify your working state.

### 2.1 Run CI Checks

```bash
composer ci:local
```

**Expected Result**: All checks should pass:
- ✓ Build Frontend Assets
- ✓ Linting (Pint, Rector, JS)
- ✓ Unit Tests with Coverage (100%)
- ✓ Type Checking (PHPStan)
- ✓ Security Audit

**If checks fail**: Fix the issues before proceeding. Do not commit failing code.

### 2.2 Verify Working State

Check what changes you have:

```bash
# Using jj
jj status

# Using git
git status
```

**Verify**:
- Only intended files are modified
- No accidentally included files (check `.gitignore` is working)
- No sensitive data or secrets in changes

### 2.3 Check Current Branch

```bash
# Using jj
jj log -r @

# Using git
git branch --show-current
```

**Expected**: You should be on `develop` branch. If not, switch:

```bash
git checkout develop
```

## 3 Commit with jj

Use jj to create a comprehensive commit with detailed description.

### 3.1 Create New Commit

```bash
jj new -m "feat: comprehensive commit message here"
```

Or use an editor for a multi-line message:

```bash
jj new
# This opens your editor (configured via $EDITOR)
```

### 3.2 Commit Message Format

Follow conventional commits with detailed description. See the workflow guide for examples.

## 4 Sync with Git Develop Branch

Export jj changes to git and push to remote develop branch.

### 4.1 Export jj Changes to Git

```bash
jj git export
```

Then push to remote:

```bash
git push origin develop
```

## 5 GitHub Workflow Verification

Monitor GitHub Actions to ensure all workflows complete successfully.

### 5.1 Check Workflow Status

```bash
gh run list --branch develop --limit 5
```

Or check in web UI at: `https://github.com/s-a-c/lw4fm5/actions`

## 6 Create PR to Main

Create a pull request from `develop` to `main` with comprehensive description.

### 6.1 Using GitHub CLI

```bash
gh pr create --base main --head develop --title "..." --body "..."
```

## 7 PR Review Process

Review your PR and address any feedback.

## 8 Fix Issues Locally

Make changes, commit with jj, and push to update the PR.

## 9 Merge Successful PR

Once all checks pass, merge into main via GitHub UI or CLI.

## 10 Sync Local Branches

After PR is merged, sync your local main and develop branches:

```bash
git fetch origin
git checkout main && git pull origin main
git checkout develop && git pull origin develop
```

## 11 Troubleshooting

See the full documentation file for detailed troubleshooting steps.

---

**Last Updated**: 2025-01-XX
**Maintainer**: Development Team
