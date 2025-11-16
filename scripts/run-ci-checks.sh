#!/bin/bash

# Standalone script to run CI checks locally
# Can be run manually: ./scripts/run-ci-checks.sh

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Running CI checks locally...${NC}\n"

# Version checks
echo -e "${YELLOW}=== Version Checks ===${NC}\n"

# Check PHP version from composer.json
PHP_REQUIREMENT=$(grep '"php"' composer.json | sed 's/.*"php": *"\([^"]*\)".*/\1/' | head -1)
if [ -z "$PHP_REQUIREMENT" ]; then
    echo -e "${RED}✗ Could not determine PHP requirement from composer.json${NC}\n"
    exit 1
fi

# Extract minimum PHP version (e.g., ^8.4 -> 8.4)
PHP_MIN=$(echo "$PHP_REQUIREMENT" | sed 's/\^//' | sed 's/~//' | sed 's/>=//' | cut -d. -f1,2)
CURRENT_PHP=$(php -r "echo PHP_VERSION;" | cut -d. -f1,2)

# Compare versions (simple numeric comparison for major.minor)
PHP_MIN_MAJOR=$(echo "$PHP_MIN" | cut -d. -f1)
PHP_MIN_MINOR=$(echo "$PHP_MIN" | cut -d. -f2)
CURRENT_PHP_MAJOR=$(echo "$CURRENT_PHP" | cut -d. -f1)
CURRENT_PHP_MINOR=$(echo "$CURRENT_PHP" | cut -d. -f2)

if [ "$CURRENT_PHP_MAJOR" -lt "$PHP_MIN_MAJOR" ] || ([ "$CURRENT_PHP_MAJOR" -eq "$PHP_MIN_MAJOR" ] && [ "$CURRENT_PHP_MINOR" -lt "$PHP_MIN_MINOR" ]); then
    echo -e "${RED}✗ PHP version mismatch${NC}"
    echo -e "  Required: ${PHP_REQUIREMENT} (minimum ${PHP_MIN})"
    echo -e "  Current: ${CURRENT_PHP}"
    echo -e "  Please use PHP ${PHP_MIN} or higher\n"
    exit 1
else
    echo -e "${GREEN}✓ PHP version OK${NC} (${CURRENT_PHP}, required: ${PHP_REQUIREMENT})\n"
fi

# Check Node version from package.json
if command -v node &> /dev/null; then
    NODE_REQUIREMENT=$(grep '"node"' package.json | sed 's/.*"node": *">=\([^"]*\)".*/\1/' | head -1)
    if [ -n "$NODE_REQUIREMENT" ]; then
        CURRENT_NODE=$(node -v | sed 's/v//' | cut -d. -f1)
        if [ "$CURRENT_NODE" -lt "$NODE_REQUIREMENT" ]; then
            echo -e "${RED}✗ Node version mismatch${NC}"
            echo -e "  Required: >=${NODE_REQUIREMENT}"
            echo -e "  Current: ${CURRENT_NODE}"
            echo -e "  Please use Node ${NODE_REQUIREMENT} or higher\n"
            exit 1
        else
            echo -e "${GREEN}✓ Node version OK${NC} (${CURRENT_NODE}, required: >=${NODE_REQUIREMENT})\n"
        fi
    fi
else
    echo -e "${YELLOW}⚠ Node not found, skipping Node version check${NC}\n"
fi

# Check Bun version from package.json
if command -v bun &> /dev/null; then
    BUN_REQUIREMENT=$(grep '"bun"' package.json | sed 's/.*"bun": *">=\([^"]*\)".*/\1/' | head -1)
    if [ -n "$BUN_REQUIREMENT" ]; then
        CURRENT_BUN=$(bun -v | cut -d. -f1,2)
        BUN_MIN_MAJOR=$(echo "$BUN_REQUIREMENT" | cut -d. -f1)
        BUN_MIN_MINOR=$(echo "$BUN_REQUIREMENT" | cut -d. -f2)
        CURRENT_BUN_MAJOR=$(echo "$CURRENT_BUN" | cut -d. -f1)
        CURRENT_BUN_MINOR=$(echo "$CURRENT_BUN" | cut -d. -f2)
        
        if [ "$CURRENT_BUN_MAJOR" -lt "$BUN_MIN_MAJOR" ] || ([ "$CURRENT_BUN_MAJOR" -eq "$BUN_MIN_MAJOR" ] && [ "$CURRENT_BUN_MINOR" -lt "$BUN_MIN_MINOR" ]); then
            echo -e "${RED}✗ Bun version mismatch${NC}"
            echo -e "  Required: >=${BUN_REQUIREMENT}"
            echo -e "  Current: ${CURRENT_BUN}"
            echo -e "  Please use Bun ${BUN_REQUIREMENT} or higher\n"
            exit 1
        else
            echo -e "${GREEN}✓ Bun version OK${NC} (${CURRENT_BUN}, required: >=${BUN_REQUIREMENT})\n"
        fi
    fi
else
    echo -e "${YELLOW}⚠ Bun not found, skipping Bun version check${NC}\n"
fi

# Track failures
FAILED=0

# Function to run a check
run_check() {
    local name=$1
    local command=$2

    echo -e "${YELLOW}Running: ${name}...${NC}"
    if eval "$command"; then
        echo -e "${GREEN}✓ ${name} passed${NC}\n"
    else
        echo -e "${RED}✗ ${name} failed${NC}\n"
        FAILED=1
        return 1
    fi
}

# Core Quality Checks (same as GitHub Actions)
echo -e "${YELLOW}=== Core Quality Checks ===${NC}\n"

run_check "Linting (Pint, Rector, JS)" "composer test:lint" || FAILED=1

run_check "Unit Tests with Coverage" "composer test:unit" || FAILED=1

run_check "Type Checking (PHPStan)" "composer test:types" || FAILED=1

run_check "Security Audit" "composer security:audit" || FAILED=1

# Policy Checksum Monitor
echo -e "${YELLOW}=== Policy Checks ===${NC}\n"
# Skip Policy Checksum Monitor on PHP 8.4 due to Monolog compatibility issue
PHP_VERSION=$(php -r "echo PHP_VERSION;" | cut -d. -f1,2)
if [ "$PHP_VERSION" = "8.4" ]; then
    echo -e "${YELLOW}⚠ Policy Checksum Monitor skipped: Monolog compatibility issue with PHP 8.4${NC}\n"
    echo -e "${YELLOW}  This is a known issue: PHP 8.4's native PSR interfaces conflict with Monolog${NC}\n"
    echo -e "${YELLOW}  Consider using PHP 8.3 or wait for Monolog PHP 8.4 compatibility update${NC}\n"
else
    run_check "Policy Checksum Monitor" "php artisan policy:checksum-monitor" || FAILED=1
fi

# Summary
echo -e "\n${YELLOW}=== Summary ===${NC}\n"

if [ $FAILED -eq 0 ]; then
    echo -e "${GREEN}All CI checks passed! ✓${NC}\n"
    exit 0
else
    echo -e "${RED}CI checks failed. Please fix the issues above.${NC}\n"
    exit 1
fi
