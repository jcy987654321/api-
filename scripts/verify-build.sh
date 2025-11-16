#!/bin/bash

# Build Verification Script
# Verifies that the production build meets all requirements

set -e

GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

ERRORS=0
WARNINGS=0

echo "======================================"
echo "Build Verification Script"
echo "======================================"
echo ""

# Check if build directory exists
echo -n "Checking build directory... "
if [ -d "public/build" ]; then
    echo -e "${GREEN}✓${NC}"
else
    echo -e "${RED}✗ Build directory not found${NC}"
    echo "Run 'npm run build' first"
    exit 1
fi

# Check if manifest.json exists
echo -n "Checking manifest.json... "
if [ -f "public/build/manifest.json" ]; then
    echo -e "${GREEN}✓${NC}"
else
    echo -e "${RED}✗ manifest.json not found${NC}"
    ERRORS=$((ERRORS + 1))
fi

# Check for asset files
echo -n "Checking for CSS assets... "
CSS_COUNT=$(find public/build -name "*.css" | wc -l)
if [ "$CSS_COUNT" -gt 0 ]; then
    echo -e "${GREEN}✓ Found $CSS_COUNT CSS files${NC}"
else
    echo -e "${RED}✗ No CSS files found${NC}"
    ERRORS=$((ERRORS + 1))
fi

echo -n "Checking for JS assets... "
JS_COUNT=$(find public/build -name "*.js" | wc -l)
if [ "$JS_COUNT" -gt 0 ]; then
    echo -e "${GREEN}✓ Found $JS_COUNT JS files${NC}"
else
    echo -e "${RED}✗ No JS files found${NC}"
    ERRORS=$((ERRORS + 1))
fi

# Check for hashed filenames
echo -n "Checking for cache-busted filenames... "
HASHED_COUNT=$(find public/build/assets -type f | grep -E '\-[a-f0-9]{8,}\.' | wc -l)
if [ "$HASHED_COUNT" -gt 0 ]; then
    echo -e "${GREEN}✓ Found $HASHED_COUNT files with hashed names${NC}"
else
    echo -e "${YELLOW}⚠ No hashed filenames found (cache busting may not work)${NC}"
    WARNINGS=$((WARNINGS + 1))
fi

# Check file sizes
echo ""
echo "Asset Size Analysis:"
echo "--------------------"

# Check largest files
echo "Largest assets:"
find public/build -type f -exec ls -lh {} \; | sort -k5 -hr | head -5 | awk '{print "  " $9 " - " $5}'

# Check total build size
TOTAL_SIZE=$(du -sh public/build | cut -f1)
echo ""
echo "Total build size: $TOTAL_SIZE"

# Check for gzipped files
echo ""
echo -n "Checking for gzip compression... "
GZ_COUNT=$(find public/build -name "*.gz" | wc -l)
if [ "$GZ_COUNT" -gt 0 ]; then
    echo -e "${GREEN}✓ Found $GZ_COUNT gzipped files${NC}"
else
    echo -e "${YELLOW}⚠ No gzipped files found${NC}"
    echo "  Consider enabling compression in vite.config.js"
    WARNINGS=$((WARNINGS + 1))
fi

# Check for vendor chunks
echo -n "Checking for code splitting... "
CHUNK_COUNT=$(find public/build/assets -name "*vendor*.js" -o -name "*chunk*.js" | wc -l)
if [ "$CHUNK_COUNT" -gt 0 ]; then
    echo -e "${GREEN}✓ Found $CHUNK_COUNT code-split chunks${NC}"
else
    echo -e "${YELLOW}⚠ No vendor chunks found (may impact initial load time)${NC}"
    WARNINGS=$((WARNINGS + 1))
fi

# Check for source maps (should not be in production)
echo -n "Checking for source maps... "
MAP_COUNT=$(find public/build -name "*.map" | wc -l)
if [ "$MAP_COUNT" -eq 0 ]; then
    echo -e "${GREEN}✓ No source maps (production-ready)${NC}"
else
    echo -e "${YELLOW}⚠ Found $MAP_COUNT source maps${NC}"
    echo "  Consider removing source maps for production"
    WARNINGS=$((WARNINGS + 1))
fi

# Verify no CDN dependencies (check manifest for CDN URLs)
echo -n "Checking for CDN dependencies... "
if grep -q "cdn\|cloudflare\|jsdelivr\|unpkg" public/build/manifest.json 2>/dev/null; then
    echo -e "${RED}✗ CDN dependencies detected${NC}"
    ERRORS=$((ERRORS + 1))
else
    echo -e "${GREEN}✓ No CDN dependencies${NC}"
fi

# Performance budget checks
echo ""
echo "Performance Budget Checks:"
echo "-------------------------"

# Check individual JS file sizes (should be < 250KB)
echo "Checking JavaScript bundle sizes..."
find public/build/assets -name "*.js" -not -name "*.map" | while read file; do
    SIZE=$(stat -f%z "$file" 2>/dev/null || stat -c%s "$file" 2>/dev/null)
    SIZE_KB=$((SIZE / 1024))
    
    if [ "$SIZE_KB" -gt 250 ]; then
        echo -e "  ${RED}✗ $(basename $file): ${SIZE_KB}KB (exceeds 250KB budget)${NC}"
        ERRORS=$((ERRORS + 1))
    elif [ "$SIZE_KB" -gt 200 ]; then
        echo -e "  ${YELLOW}⚠ $(basename $file): ${SIZE_KB}KB (close to budget)${NC}"
        WARNINGS=$((WARNINGS + 1))
    else
        echo -e "  ${GREEN}✓ $(basename $file): ${SIZE_KB}KB${NC}"
    fi
done

# Check CSS file sizes (should be < 100KB)
echo ""
echo "Checking CSS bundle sizes..."
find public/build/assets -name "*.css" | while read file; do
    SIZE=$(stat -f%z "$file" 2>/dev/null || stat -c%s "$file" 2>/dev/null)
    SIZE_KB=$((SIZE / 1024))
    
    if [ "$SIZE_KB" -gt 100 ]; then
        echo -e "  ${RED}✗ $(basename $file): ${SIZE_KB}KB (exceeds 100KB budget)${NC}"
        ERRORS=$((ERRORS + 1))
    elif [ "$SIZE_KB" -gt 75 ]; then
        echo -e "  ${YELLOW}⚠ $(basename $file): ${SIZE_KB}KB (close to budget)${NC}"
        WARNINGS=$((WARNINGS + 1))
    else
        echo -e "  ${GREEN}✓ $(basename $file): ${SIZE_KB}KB${NC}"
    fi
done

# Summary
echo ""
echo "======================================"
echo "Summary"
echo "======================================"
echo -e "Errors:   ${RED}$ERRORS${NC}"
echo -e "Warnings: ${YELLOW}$WARNINGS${NC}"
echo ""

if [ "$ERRORS" -gt 0 ]; then
    echo -e "${RED}Build verification FAILED with $ERRORS error(s)${NC}"
    exit 1
elif [ "$WARNINGS" -gt 0 ]; then
    echo -e "${YELLOW}Build verification passed with $WARNINGS warning(s)${NC}"
    echo "Consider addressing warnings for optimal performance"
    exit 0
else
    echo -e "${GREEN}Build verification PASSED${NC}"
    echo "All checks passed successfully!"
    exit 0
fi
