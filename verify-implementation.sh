#!/bin/bash

# Public Layout Implementation Verification Script
# This script verifies that all required files are present and valid

echo "========================================="
echo "Public Layout Implementation Verification"
echo "========================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Track results
PASSED=0
FAILED=0
WARNINGS=0

# Function to check file exists
check_file() {
    if [ -f "$1" ]; then
        echo -e "${GREEN}✓${NC} $1"
        ((PASSED++))
        return 0
    else
        echo -e "${RED}✗${NC} $1 (missing)"
        ((FAILED++))
        return 1
    fi
}

# Function to check directory exists
check_dir() {
    if [ -d "$1" ]; then
        echo -e "${GREEN}✓${NC} $1/"
        ((PASSED++))
        return 0
    else
        echo -e "${RED}✗${NC} $1/ (missing)"
        ((FAILED++))
        return 1
    fi
}

echo "Checking Directory Structure..."
echo "-------------------------------"
check_dir "resources/views/layouts"
check_dir "resources/views/public"
check_dir "resources/scss/public"
check_dir "public/css"
check_dir "public/js"
echo ""

echo "Checking Blade Templates..."
echo "---------------------------"
check_file "resources/views/layouts/public.blade.php"
check_file "resources/views/public/home.blade.php"
check_file "resources/views/public/features.blade.php"
check_file "resources/views/public/about.blade.php"
echo ""

echo "Checking SCSS Files..."
echo "----------------------"
check_file "resources/scss/public/_variables.scss"
check_file "resources/scss/public/_base.scss"
check_file "resources/scss/public/_components.scss"
check_file "resources/scss/public/styles.scss"
echo ""

echo "Checking Compiled Assets..."
echo "---------------------------"
check_file "public/css/public.css"
check_file "public/js/public.js"
echo ""

echo "Checking Documentation..."
echo "-------------------------"
check_file "README.md"
check_file "PUBLIC_LAYOUT_DOCS.md"
check_file "IMPLEMENTATION_GUIDE.md"
check_file "TESTING_CHECKLIST.md"
check_file "IMPLEMENTATION_SUMMARY.md"
echo ""

echo "Checking Configuration Files..."
echo "-------------------------------"
check_file "package.json"
check_file ".gitignore"
check_file "demo.html"
echo ""

echo "Validating CSS File..."
echo "----------------------"
if [ -f "public/css/public.css" ]; then
    LINES=$(wc -l < public/css/public.css)
    if [ $LINES -gt 100 ]; then
        echo -e "${GREEN}✓${NC} CSS file has $LINES lines (expected > 100)"
        ((PASSED++))
    else
        echo -e "${YELLOW}⚠${NC} CSS file has only $LINES lines (may be incomplete)"
        ((WARNINGS++))
    fi
else
    echo -e "${RED}✗${NC} CSS file not found"
    ((FAILED++))
fi
echo ""

echo "Validating JavaScript File..."
echo "-----------------------------"
if [ -f "public/js/public.js" ]; then
    if command -v node &> /dev/null; then
        if node -c public/js/public.js 2>&1; then
            echo -e "${GREEN}✓${NC} JavaScript syntax is valid"
            ((PASSED++))
        else
            echo -e "${RED}✗${NC} JavaScript syntax error"
            ((FAILED++))
        fi
    else
        echo -e "${YELLOW}⚠${NC} Node.js not found, skipping JS validation"
        ((WARNINGS++))
    fi
else
    echo -e "${RED}✗${NC} JavaScript file not found"
    ((FAILED++))
fi
echo ""

echo "Checking Key Features in Files..."
echo "----------------------------------"

# Check for theme system in JavaScript
if grep -q "initThemeToggle" public/js/public.js 2>/dev/null; then
    echo -e "${GREEN}✓${NC} Theme toggle system found in JS"
    ((PASSED++))
else
    echo -e "${RED}✗${NC} Theme toggle system not found in JS"
    ((FAILED++))
fi

# Check for PJAX in JavaScript
if grep -q "initPJAX" public/js/public.js 2>/dev/null; then
    echo -e "${GREEN}✓${NC} PJAX navigation found in JS"
    ((PASSED++))
else
    echo -e "${RED}✗${NC} PJAX navigation not found in JS"
    ((FAILED++))
fi

# Check for scroll controls in JavaScript
if grep -q "initScrollControls" public/js/public.js 2>/dev/null; then
    echo -e "${GREEN}✓${NC} Scroll controls found in JS"
    ((PASSED++))
else
    echo -e "${RED}✗${NC} Scroll controls not found in JS"
    ((FAILED++))
fi

# Check for responsive breakpoints in CSS
if grep -q "@media" public/css/public.css 2>/dev/null; then
    echo -e "${GREEN}✓${NC} Responsive breakpoints found in CSS"
    ((PASSED++))
else
    echo -e "${RED}✗${NC} Responsive breakpoints not found in CSS"
    ((FAILED++))
fi

# Check for theme variables in CSS
if grep -q "data-theme" public/css/public.css 2>/dev/null; then
    echo -e "${GREEN}✓${NC} Theme system variables found in CSS"
    ((PASSED++))
else
    echo -e "${RED}✗${NC} Theme system variables not found in CSS"
    ((FAILED++))
fi

# Check for accessibility features in layout
if grep -q "skip-link" resources/views/layouts/public.blade.php 2>/dev/null; then
    echo -e "${GREEN}✓${NC} Accessibility features found in layout"
    ((PASSED++))
else
    echo -e "${RED}✗${NC} Accessibility features not found in layout"
    ((FAILED++))
fi

# Check for PJAX container in layout
if grep -q "pjax-container" resources/views/layouts/public.blade.php 2>/dev/null; then
    echo -e "${GREEN}✓${NC} PJAX container found in layout"
    ((PASSED++))
else
    echo -e "${RED}✗${NC} PJAX container not found in layout"
    ((FAILED++))
fi

echo ""
echo "========================================="
echo "Verification Results"
echo "========================================="
echo -e "${GREEN}Passed:${NC} $PASSED"
echo -e "${RED}Failed:${NC} $FAILED"
echo -e "${YELLOW}Warnings:${NC} $WARNINGS"
echo ""

if [ $FAILED -eq 0 ]; then
    echo -e "${GREEN}✓ All checks passed!${NC}"
    echo "The public layout implementation is complete and ready."
    exit 0
elif [ $FAILED -le 2 ]; then
    echo -e "${YELLOW}⚠ Implementation mostly complete with minor issues.${NC}"
    exit 1
else
    echo -e "${RED}✗ Implementation has significant issues.${NC}"
    exit 2
fi
