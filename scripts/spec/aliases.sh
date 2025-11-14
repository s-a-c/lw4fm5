#!/usr/bin/env bash
# Shell aliases for spec-kit workflow
# Source this file in your shell: source scripts/spec/aliases.sh

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Main workflow aliases
alias spec-start='$SCRIPT_DIR/start-spec.sh'
alias spec-plan='$SCRIPT_DIR/generate-plan.sh'
alias spec-tasks='$SCRIPT_DIR/generate-tasks.sh'
alias spec-clarify='$SCRIPT_DIR/generate-clarify.sh'
alias spec-checklist='$SCRIPT_DIR/generate-checklist.sh'
alias spec-finish='$SCRIPT_DIR/finish-spec.sh'
alias spec-feature='$SCRIPT_DIR/start-feature.sh'

echo "Spec-kit aliases loaded:"
echo "  spec-start      - Start a new specification"
echo "  spec-plan       - Generate plan from spec"
echo "  spec-tasks      - Generate tasks from plan"
echo "  spec-clarify    - Generate clarifications"
echo "  spec-checklist  - Generate checklists"
echo "  spec-finish     - Finish and merge spec"
echo "  spec-feature    - Start feature branch from spec"
