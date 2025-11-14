#!/usr/bin/env bash
# Prepare context for /speckit.clarify command

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

# Check if spec directory exists
if [ ! -d "$SPEC_DIR" ]; then
    print_error "Spec directory not found: $SPEC_DIR"
    exit 1
fi

# Check if spec.md exists
if [ ! -f "$SPEC_FILE" ]; then
    print_error "Specification file not found: $SPEC_FILE"
    exit 1
fi

print_info "Generating clarifications for specification: $SPEC_ID"

# Print AI prompt for user
echo ""
print_info "=========================================="
print_info "AI Agent Prompt:"
print_info "=========================================="
echo ""
echo "Use '/speckit.clarify' with spec at:"
echo "  \`$SPEC_FILE\`"
echo ""
print_info "Specification Context:"
echo "  - Spec ID: $SPEC_ID"
echo "  - Spec File: $SPEC_FILE"
echo "  - Spec Directory: $SPEC_DIR"
echo ""
print_info "Execute this command in your AI agent to generate clarifications."
