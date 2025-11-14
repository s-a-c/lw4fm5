#!/usr/bin/env bash
# Start a new specification branch and directory structure

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
    print_error "Usage: $0 <spec-identifier> [description]"
    print_info "Example: $0 001-user-auth \"User authentication system\""
    exit 1
fi

SPEC_ID="$1"
DESCRIPTION="${2:-}"

# Validate spec identifier format (###-feature-name)
if ! [[ "$SPEC_ID" =~ ^[0-9]{3}-[a-z0-9-]+$ ]]; then
    print_error "Invalid spec identifier format: $SPEC_ID"
    print_info "Format must be: ###-feature-name (e.g., 001-user-auth)"
    exit 1
fi

SPEC_BRANCH="spec/$SPEC_ID"
SPEC_DIR="$PROJECT_ROOT/specs/$SPEC_ID"

# Check if spec directory already exists
if [ -d "$SPEC_DIR" ]; then
    print_error "Spec directory already exists: $SPEC_DIR"
    exit 1
fi

# Check if branch already exists
if git rev-parse --verify "$SPEC_BRANCH" >/dev/null 2>&1; then
    print_error "Branch already exists: $SPEC_BRANCH"
    exit 1
fi

print_info "Starting new specification: $SPEC_ID"

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

# Check for jj availability
USE_JJ=false
if command -v jj &> /dev/null; then
    USE_JJ=true
    print_info "Using Jujutsu (jj) for version control"
fi

# Create spec branch
print_info "Creating spec branch: $SPEC_BRANCH"
if [ "$USE_JJ" = true ]; then
    jj branch create "$SPEC_BRANCH"
    jj new -m "Start specification: $SPEC_ID${DESCRIPTION:+ - }$DESCRIPTION"
else
    git checkout -b "$SPEC_BRANCH"
fi

# Create spec directory structure
print_info "Creating spec directory structure: $SPEC_DIR"
mkdir -p "$SPEC_DIR"
mkdir -p "$SPEC_DIR/checklists"
mkdir -p "$SPEC_DIR/checklists/spec"
mkdir -p "$SPEC_DIR/checklists/plan"
mkdir -p "$SPEC_DIR/contracts"

# Create template spec.md
SPEC_FILE="$SPEC_DIR/spec.md"
cat > "$SPEC_FILE" <<EOF
# Feature Specification: ${SPEC_ID//-/ }

**Feature Branch**: \`$SPEC_ID\`
**Created**: $(date +%Y-%m-%d)
**Status**: Draft
${DESCRIPTION:+**Description**: $DESCRIPTION}

## User Scenarios & Testing *(mandatory)*

### User Story 1 - [Story Title] (Priority: P1)

[Description of user story...]

**Why this priority**: [Explanation of priority...]

**Independent Test**: [Description of how to test independently...]

**Acceptance Scenarios**:

1. **Given** [context], **When** [action], **Then** [expected outcome]
2. **Given** [context], **When** [action], **Then** [expected outcome]

---

## Success Criteria

- [ ] Criteria 1
- [ ] Criteria 2

## Constraints

- Constraint 1
- Constraint 2

## Out of Scope

- Explicitly excluded item 1
- Explicitly excluded item 2
EOF

print_success "Created specification template: $SPEC_FILE"

# Create .gitkeep files for empty directories
touch "$SPEC_DIR/checklists/spec/.gitkeep"
touch "$SPEC_DIR/checklists/plan/.gitkeep"
touch "$SPEC_DIR/contracts/.gitkeep"

# Stage and commit initial spec
print_info "Staging initial specification"
if [ "$USE_JJ" = true ]; then
    jj new -m "Add specification template for $SPEC_ID"
else
    git add "$SPEC_DIR"
    git commit -m "spec: initialize $SPEC_ID specification"
fi

print_success "Specification started successfully!"
print_info "Spec branch: $SPEC_BRANCH"
print_info "Spec directory: $SPEC_DIR"
print_info ""
print_info "Next steps:"
print_info "1. Edit $SPEC_FILE with feature requirements"
print_info "2. Run './scripts/spec/generate-plan.sh $SPEC_ID' to generate plan"
