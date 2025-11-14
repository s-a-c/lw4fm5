#!/usr/bin/env bash
# Prepare context for /speckit.plan command and handle generated plan.md

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

SPEC_DIR="$PROJECT_ROOT/specs/$SPEC_ID"
SPEC_FILE="$SPEC_DIR/spec.md"
PLAN_FILE="$SPEC_DIR/plan.md"

# Check if spec directory exists
if [ ! -d "$SPEC_DIR" ]; then
    print_error "Spec directory not found: $SPEC_DIR"
    print_info "Run './scripts/spec/start-spec.sh $SPEC_ID' first"
    exit 1
fi

# Check if spec.md exists
if [ ! -f "$SPEC_FILE" ]; then
    print_error "Specification file not found: $SPEC_FILE"
    print_info "Create the specification file first"
    exit 1
fi

print_info "Generating plan for specification: $SPEC_ID"

# Check for jj availability
USE_JJ=false
if command -v jj &> /dev/null; then
    USE_JJ=true
fi

# Print AI prompt for user
echo ""
print_info "=========================================="
print_info "AI Agent Prompt:"
print_info "=========================================="
echo ""
echo "Use '/speckit.plan' with spec at:"
echo "  \`$SPEC_FILE\`"
echo ""
print_info "Specification Context:"
echo "  - Spec ID: $SPEC_ID"
echo "  - Spec File: $SPEC_FILE"
echo "  - Spec Directory: $SPEC_DIR"
echo "  - Project Root: $PROJECT_ROOT"
echo ""
print_warning "After AI generates plan.md, this script will detect and commit it."
print_warning "Press Enter when plan.md has been generated, or Ctrl+C to cancel..."
echo ""
read -r

# Wait for plan.md to be generated
MAX_WAIT=300  # 5 minutes
WAIT_TIME=0
WAIT_INTERVAL=2

while [ ! -f "$PLAN_FILE" ] && [ $WAIT_TIME -lt $MAX_WAIT ]; do
    sleep $WAIT_INTERVAL
    WAIT_TIME=$((WAIT_TIME + WAIT_INTERVAL))
    if [ $((WAIT_TIME % 10)) -eq 0 ]; then
        print_info "Waiting for plan.md to be generated... (${WAIT_TIME}s)"
    fi
done

if [ ! -f "$PLAN_FILE" ]; then
    print_error "plan.md not found after waiting. Please generate it manually."
    print_info "Once plan.md exists, run this script again or commit manually."
    exit 1
fi

print_success "Detected generated plan.md"

# Commit the generated plan
print_info "Committing generated plan.md"
if [ "$USE_JJ" = true ]; then
    jj new -m "spec: generate plan for $SPEC_ID"
else
    git add "$PLAN_FILE"
    git commit -m "spec: generate plan for $SPEC_ID"
fi

print_success "Plan committed successfully!"
print_info "Plan file: $PLAN_FILE"
print_info ""
print_info "Next steps:"
print_info "1. Review the generated plan.md"
print_info "2. Run './scripts/spec/generate-tasks.sh $SPEC_ID' to generate tasks"
