#!/usr/bin/env bash
# Create feature branch from spec, referencing spec in branch description

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
    print_error "Usage: $0 <spec-identifier>"
    print_info "Example: $0 001-user-auth"
    exit 1
fi

SPEC_ID="$1"

# Validate spec identifier format
if ! [[ "$SPEC_ID" =~ ^[0-9]{3}-[a-z0-9-]+$ ]]; then
    print_error "Invalid spec identifier format: $SPEC_ID"
    print_info "Format must be: ###-feature-name (e.g., 001-user-auth)"
    exit 1
fi

SPEC_BRANCH="spec/$SPEC_ID"
FEATURE_BRANCH="feature/$SPEC_ID"
SPEC_DIR="$PROJECT_ROOT/specs/$SPEC_ID"

# Check if spec directory exists
if [ ! -d "$SPEC_DIR" ]; then
    print_error "Spec directory not found: $SPEC_DIR"
    print_info "Ensure specification exists before creating feature branch"
    exit 1
fi

# Check if spec branch exists and is merged
if ! git rev-parse --verify "$SPEC_BRANCH" >/dev/null 2>&1; then
    print_warning "Spec branch not found: $SPEC_BRANCH"
    print_warning "Continuing anyway (spec may be in develop)"
fi

# Check if feature branch already exists
if git rev-parse --verify "$FEATURE_BRANCH" >/dev/null 2>&1; then
    print_error "Feature branch already exists: $FEATURE_BRANCH"
    exit 1
fi

print_info "Creating feature branch from spec: $SPEC_ID"

# Ensure we're on develop branch
CURRENT_BRANCH=$(git rev-parse --abbrev-ref HEAD)
if [ "$CURRENT_BRANCH" != "develop" ]; then
    print_warning "Not on develop branch (currently on: $CURRENT_BRANCH)"
    read -p "Switch to develop branch? (y/n) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        git checkout develop
        git pull origin develop 2>/dev/null || true
    else
        print_error "Aborted. Please switch to develop branch first."
        exit 1
    fi
fi

# Get spec commit (if spec branch exists)
SPEC_COMMIT=""
if git rev-parse --verify "$SPEC_BRANCH" >/dev/null 2>&1; then
    SPEC_COMMIT=$(git rev-parse "$SPEC_BRANCH")
fi

# Check for jj availability
USE_JJ=false
if command -v jj &> /dev/null; then
    USE_JJ=true
    print_info "Using Jujutsu (jj) for version control"
fi

# Create feature branch
print_info "Creating feature branch: $FEATURE_BRANCH"
if [ "$USE_JJ" = true ]; then
    jj branch create "$FEATURE_BRANCH"
    if [ -n "$SPEC_COMMIT" ]; then
        jj new -m "Start feature: $SPEC_ID (spec: $SPEC_BRANCH @ ${SPEC_COMMIT:0:8})"
    else
        jj new -m "Start feature: $SPEC_ID"
    fi
else
    git checkout -b "$FEATURE_BRANCH"
    if [ -n "$SPEC_COMMIT" ]; then
        git commit --allow-empty -m "Start feature: $SPEC_ID

Specification: $SPEC_BRANCH @ ${SPEC_COMMIT:0:8}
Spec location: specs/$SPEC_ID/"
    else
        git commit --allow-empty -m "Start feature: $SPEC_ID

Spec location: specs/$SPEC_ID/"
    fi
fi

print_success "Feature branch created successfully!"
print_info "Feature branch: $FEATURE_BRANCH"
print_info "Spec directory: $SPEC_DIR"
if [ -n "$SPEC_COMMIT" ]; then
    print_info "Spec commit: ${SPEC_COMMIT:0:8}"
fi
print_info ""
print_info "Specification files available:"
if [ -f "$SPEC_DIR/spec.md" ]; then
    echo "  - spec.md: $SPEC_DIR/spec.md"
fi
if [ -f "$SPEC_DIR/plan.md" ]; then
    echo "  - plan.md: $SPEC_DIR/plan.md"
fi
if [ -f "$SPEC_DIR/tasks.md" ]; then
    echo "  - tasks.md: $SPEC_DIR/tasks.md"
fi
print_info ""
print_info "Next steps:"
print_info "1. Review spec.md, plan.md, and tasks.md for implementation guidance"
print_info "2. Follow tasks.md for implementation steps"
print_info "3. Reference spec and plan during development"
