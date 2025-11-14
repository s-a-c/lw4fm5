#!/usr/bin/env bash
# Validate spec completeness and merge spec branch to develop

set -euo pipefail

# Color codes for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Script directory
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/../.." && pwd)"

# Function to print colored output
print_info() {
    echo -e "${BLUE}ℹ${NC} $1"
}

print_success() {
    echo -e "${GREEN}✓${NC} $1"
}

print_error() {
    echo -e "${RED}✗${NC} $1" >&2
}

print_warning() {
    echo -e "${YELLOW}⚠${NC} $1"
}

# Check if spec identifier is provided
if [ $# -lt 1 ]; then
    print_error "Usage: $0 <spec-identifier> [--pr]"
    print_info "Example: $0 001-user-auth"
    print_info "Example: $0 001-user-auth --pr  (opens PR instead of merging)"
    exit 1
fi

SPEC_ID="$1"
OPEN_PR=false

# Check for --pr flag
if [ $# -ge 2 ] && [ "$2" = "--pr" ]; then
    OPEN_PR=true
fi

# Validate spec identifier format
if ! [[ "$SPEC_ID" =~ ^[0-9]{3}-[a-z0-9-]+$ ]]; then
    print_error "Invalid spec identifier format: $SPEC_ID"
    print_info "Format must be: ###-feature-name (e.g., 001-user-auth)"
    exit 1
fi

SPEC_BRANCH="spec/$SPEC_ID"
SPEC_DIR="$PROJECT_ROOT/specs/$SPEC_ID"
SPEC_FILE="$SPEC_DIR/spec.md"
PLAN_FILE="$SPEC_DIR/plan.md"
TASKS_FILE="$SPEC_DIR/tasks.md"

# Check if spec directory exists
if [ ! -d "$SPEC_DIR" ]; then
    print_error "Spec directory not found: $SPEC_DIR"
    exit 1
fi

# Check if branch exists
if ! git rev-parse --verify "$SPEC_BRANCH" >/dev/null 2>&1; then
    print_error "Spec branch not found: $SPEC_BRANCH"
    exit 1
fi

print_info "Validating specification: $SPEC_ID"

# Validate required files
MISSING_FILES=()

if [ ! -f "$SPEC_FILE" ]; then
    MISSING_FILES+=("spec.md")
fi

if [ ! -f "$PLAN_FILE" ]; then
    MISSING_FILES+=("plan.md")
fi

if [ ! -f "$TASKS_FILE" ]; then
    MISSING_FILES+=("tasks.md")
fi

if [ ${#MISSING_FILES[@]} -gt 0 ]; then
    print_error "Missing required files:"
    for file in "${MISSING_FILES[@]}"; do
        echo "  - $file"
    done
    print_info "Generate missing files before finishing spec"
    exit 1
fi

print_success "All required files present"

# Check current branch
CURRENT_BRANCH=$(git rev-parse --abbrev-ref HEAD)
if [ "$CURRENT_BRANCH" != "$SPEC_BRANCH" ]; then
    print_warning "Not on spec branch (currently on: $CURRENT_BRANCH)"
    read -p "Switch to spec branch $SPEC_BRANCH? (y/n) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        git checkout "$SPEC_BRANCH"
    else
        print_error "Aborted. Please switch to spec branch first."
        exit 1
    fi
fi

# Check for jj availability
USE_JJ=false
if command -v jj &> /dev/null; then
    USE_JJ=true
    print_info "Using Jujutsu (jj) for version control"
fi

# Ensure develop is up to date
print_info "Updating develop branch"
git fetch origin develop 2>/dev/null || true
if [ "$USE_JJ" = true ]; then
    jj git fetch
    jj rebase -d @git/develop
else
    git checkout develop
    git pull origin develop 2>/dev/null || true
    git checkout "$SPEC_BRANCH"
fi

# Merge spec branch to develop
if [ "$OPEN_PR" = true ]; then
    print_info "Opening pull request instead of merging"
    print_info "Push spec branch and create PR manually:"
    echo ""
    echo "  git push origin $SPEC_BRANCH"
    echo "  # Then create PR from $SPEC_BRANCH to develop"
else
    print_info "Merging spec branch to develop"
    if [ "$USE_JJ" = true ]; then
        jj checkout develop
        jj merge "$SPEC_BRANCH"
        jj git push
    else
        git checkout develop
        git merge "$SPEC_BRANCH" --no-ff -m "Merge spec: $SPEC_ID"
        git push origin develop 2>/dev/null || print_warning "Failed to push to origin (push manually if needed)"
    fi

    print_success "Spec branch merged to develop successfully!"
fi

print_info "Specification finalized: $SPEC_ID"
print_info ""
print_info "Next steps:"
print_info "1. Review merged specification in develop branch"
print_info "2. Run './scripts/spec/start-feature.sh $SPEC_ID' to create feature branch"
