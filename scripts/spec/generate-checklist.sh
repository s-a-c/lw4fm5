#!/usr/bin/env bash
# Prepare context for /speckit.checklist command

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

print_error() {
    echo -e "${RED}✗${NC} $1" >&2
}

# Check if spec identifier is provided
if [ $# -lt 1 ]; then
    print_error "Usage: $0 <spec-identifier> [spec|plan]"
    print_info "Example: $0 001-user-auth spec"
    print_info "Example: $0 001-user-auth plan"
    exit 1
fi

SPEC_ID="$1"
CHECKLIST_TYPE="${2:-spec}"

# Validate checklist type
if [ "$CHECKLIST_TYPE" != "spec" ] && [ "$CHECKLIST_TYPE" != "plan" ]; then
    print_error "Invalid checklist type: $CHECKLIST_TYPE"
    print_info "Must be 'spec' or 'plan'"
    exit 1
fi

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
    exit 1
fi

# Check if required file exists
if [ "$CHECKLIST_TYPE" = "spec" ] && [ ! -f "$SPEC_FILE" ]; then
    print_error "Specification file not found: $SPEC_FILE"
    exit 1
fi

if [ "$CHECKLIST_TYPE" = "plan" ] && [ ! -f "$PLAN_FILE" ]; then
    print_error "Plan file not found: $PLAN_FILE"
    print_info "Run './scripts/spec/generate-plan.sh $SPEC_ID' first"
    exit 1
fi

print_info "Generating $CHECKLIST_TYPE checklist for specification: $SPEC_ID"

# Determine input file
if [ "$CHECKLIST_TYPE" = "spec" ]; then
    INPUT_FILE="$SPEC_FILE"
else
    INPUT_FILE="$PLAN_FILE"
fi

# Print AI prompt for user
echo ""
print_info "=========================================="
print_info "AI Agent Prompt:"
print_info "=========================================="
echo ""
echo "Use '/speckit.checklist' with $CHECKLIST_TYPE at:"
echo "  \`$INPUT_FILE\`"
echo ""
print_info "Context:"
echo "  - Spec ID: $SPEC_ID"
echo "  - Checklist Type: $CHECKLIST_TYPE"
echo "  - Input File: $INPUT_FILE"
echo "  - Checklists Directory: $SPEC_DIR/checklists/$CHECKLIST_TYPE"
echo ""
print_info "Execute this command in your AI agent to generate checklists."
