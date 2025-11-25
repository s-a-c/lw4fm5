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

Follow conventional commits with detailed description. Example formats:

```
feat: add user authentication flow

Implements OAuth2 authentication with Google and GitHub providers.
Adds user session management and token refresh logic.

fix: resolve crash on startup when config missing

Handles missing configuration gracefully by providing default values
and logging warnings instead of crashing the application.

docs: update README with setup instructions

Adds comprehensive setup guide including environment variables,
database migrations, and dependency installation steps.

refactor: simplify data processing logic

Extracts complex data transformation into separate service class
for better testability and maintainability.
```

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

Or check in web UI at: `https://github.com/<owner>/<repo>/actions`

## 6 Create PR to Main

Create a pull request from `develop` to `main` with comprehensive description.

### 6.1 Using GitHub CLI

```bash
gh pr create --base main --head develop --title "..." --body "..."
```

## 7 PR Review Process

Review your PR and address any feedback from reviewers or automated tools.

### 7.1 Self-Review Checklist

Before requesting review, verify:
- [ ] All CI checks are passing
- [ ] Code follows project conventions
- [ ] Tests are included and passing
- [ ] Documentation is updated (if needed)
- [ ] No sensitive data or secrets in changes
- [ ] Commit messages are clear and descriptive

### 7.2 Address Review Comments

When reviewers provide feedback:
1. Read all comments carefully
2. Ask for clarification if needed
3. Make changes locally
4. Commit fixes and push updates
5. Respond to comments explaining your changes

## 8 Fix Issues Locally

If issues are found during review, fix them locally and push updates:

### 8.1 Make Changes

```bash
# Make necessary changes to files
# Test locally to ensure fixes work
composer ci:local
```

### 8.2 Commit and Push

```bash
# Commit fixes with jj
jj new -m "fix: address review feedback

- Fixed linting issues
- Updated documentation
- Added missing test cases"

# Sync to git and push
jj git export
git push origin develop
```

The PR will automatically update with your new commits.

## 9 Merge Successful PR

Once all checks pass and reviews are approved, merge the PR:

### 9.1 Using GitHub UI

1. Navigate to the PR page
2. Click "Merge pull request"
3. Choose merge type (squash and merge, merge commit, or rebase and merge)
4. Confirm the merge

### 9.2 Using GitHub CLI

```bash
gh pr merge <PR_NUMBER> --squash
# or
gh pr merge <PR_NUMBER> --merge
# or
gh pr merge <PR_NUMBER> --rebase
```

### 9.3 After Merge

- The PR will be automatically closed
- The branch can be deleted (if configured)
- Main branch will be updated with your changes

## 10 Sync Local Branches

After PR is merged, sync your local main and develop branches:

```bash
# Update main branch with merged changes
git checkout main && git pull origin main

# Update develop branch (should already be up to date, but sync to be safe)
git checkout develop && git pull origin develop

# If using jj, sync jj with git
jj git fetch
jj git pull
```

## 11 Troubleshooting

Common issues and solutions:

### 11.1 Pre-Push Hooks Fail

If pre-push hooks fail:
- Fix the reported issues (linting, tests, etc.)
- Run `composer ci:local` to verify locally
- Commit fixes and try pushing again

### 11.2 Workflow Failures

If GitHub Actions workflows fail:
- Check the workflow logs for specific errors
- Reproduce the issue locally if possible
- Fix the issue and push updates
- Workflows will re-run automatically

### 11.3 Merge Conflicts

If merge conflicts occur:
- Fetch latest changes: `git fetch origin`
- Rebase your branch: `git rebase origin/main`
- Resolve conflicts manually
- Continue rebase: `git rebase --continue`
- Force push (if needed): `git push origin develop --force-with-lease`

### 11.4 jj/Git Sync Issues

If jj and git are out of sync:
- Export jj changes: `jj git export`
- Check git status: `git status`
- If needed, reset and re-export: `jj git export --reset`

### 11.5 Branch Protection Rules

If you can't push directly to main:
- This is expected - use PR workflow
- Create PR from develop to main
- Get required approvals
- Merge via PR (not direct push)

---

**Last Updated**: 2025-11-25
**Maintainer**: Development Team
