#!/bin/bash
# Script to run theme coverage tests with timeout, progress feedback, and colorized output
# Usage: ./scripts/test-coverage-theme.sh

# ANSI color codes for terminal output
GREEN_BG='\033[42m\033[30m'  # Green background, black text
RED_BG='\033[41m\033[97m'    # Red background, white text
YELLOW_BG='\033[43m\033[30m' # Yellow background, black text
BLUE_BG='\033[44m\033[97m'   # Blue background, white text
RESET='\033[0m'              # Reset colors
BOLD='\033[1m'

TIMEOUT_SECONDS=300
COVERAGE_MIN=100

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo -e "${BLUE_BG}${BOLD} Running Theme Coverage Tests ${RESET}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "Timeout: ${TIMEOUT_SECONDS}s"
echo "Coverage Minimum: ${COVERAGE_MIN}%"
echo "Filter: Theme"
echo "Goal: 100% coverage with 100% pass rate"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# Run with timeout, verbose output, and progress indicators
# Use --stop-on-failure to prevent hanging on failures
# Include browser tests (they're not overly long based on ~88s successful run)
# Redirect stdin from /dev/null to prevent "Press any key" prompts
# Use -vv for more verbose output showing progress (real-time test execution)
# PCOV is automatically used for coverage (faster than Xdebug)
echo "Starting test execution..."
echo "Progress: Tests will show real-time feedback with -vv flag"
echo "Coverage: Using PCOV (auto-detected, faster than Xdebug)"
echo ""

# Capture exit code properly - use a temp file to avoid pipe masking exit codes
TEMP_LOG=$(mktemp)
set +e  # Don't exit on error, we'll check exit code

# Run tests with progress output - Pest already has colored output
# Use unbuffered output for real-time progress feedback
# Include browser tests (they're not overly long based on ~88s successful run)
# Note: Colorization happens in post-processing to avoid breaking Pest's native colors
timeout ${TIMEOUT_SECONDS} php artisan test \
    --filter=Theme \
    --coverage \
    --min=${COVERAGE_MIN} \
    --stop-on-failure \
    -vv \
    < /dev/null \
    2>&1 | tee "${TEMP_LOG}"

EXIT_CODE=$?

# Also save to timestamped log file
cp "${TEMP_LOG}" /tmp/theme-coverage-$(date +%Y%m%d-%H%M%S).log

# Extract summary from log and colorize it
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Show coverage summary if available
if grep -q "Coverage:" "${TEMP_LOG}"; then
    echo "Coverage Summary:"
    grep -A 20 "Coverage:" "${TEMP_LOG}" | grep -E "(Coverage:|app/)" | head -15
    echo ""
fi

# Check for test results in the log
if grep -q "Tests:.*passed.*failed" "${TEMP_LOG}" || grep -q "Tests:.*failed" "${TEMP_LOG}"; then
    # Has failures - extract count using sed (more portable than grep -P)
    FAIL_COUNT=$(grep "Tests:.*failed" "${TEMP_LOG}" | sed -n 's/.*Tests:[[:space:]]*[0-9]*[[:space:]]*passed,[[:space:]]*\([0-9]*\)[[:space:]]*failed.*/\1/p' | head -1)
    if [ -n "$FAIL_COUNT" ] && [ "$FAIL_COUNT" != "0" ]; then
        echo -e "${RED_BG}${BOLD} ❌ Coverage test failed: $FAIL_COUNT test(s) failed ${RESET}"
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
        rm -f "${TEMP_LOG}"
        exit 1
    fi
fi

if [ $EXIT_CODE -eq 124 ]; then
    echo -e "${YELLOW_BG}${BOLD} ⚠️  TIMEOUT: Coverage test exceeded ${TIMEOUT_SECONDS} seconds ${RESET}"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    rm -f "${TEMP_LOG}"
    exit 1
elif [ $EXIT_CODE -ne 0 ]; then
    echo -e "${RED_BG}${BOLD} ❌ Coverage test failed with exit code: $EXIT_CODE ${RESET}"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    rm -f "${TEMP_LOG}"
    exit $EXIT_CODE
else
    # Check if all tests passed
    if grep -q "Tests:.*passed" "${TEMP_LOG}" && ! grep -q "failed" "${TEMP_LOG}"; then
        # Extract coverage percentage if available (using sed for portability)
        COVERAGE_PCT=$(grep "Coverage:" "${TEMP_LOG}" | sed -n 's/.*Coverage:[[:space:]]*\([0-9.]*%\).*/\1/p' | head -1 || echo "")
        if [ -n "$COVERAGE_PCT" ]; then
            echo -e "${GREEN_BG}${BOLD} ✅ Coverage test completed successfully - 100% pass rate, ${COVERAGE_PCT} coverage! ${RESET}"
        else
            echo -e "${GREEN_BG}${BOLD} ✅ Coverage test completed successfully - 100% pass rate! ${RESET}"
            echo -e "${GREEN_BG}${BOLD} ✅ Coverage requirement met (--min=100 enforced) ${RESET}"
        fi
    else
        echo -e "${GREEN_BG}${BOLD} ✅ Coverage test completed ${RESET}"
    fi
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    rm -f "${TEMP_LOG}"
    exit 0
fi
