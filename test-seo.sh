#!/bin/bash

# Simple integration test script for SEO automation features

echo "Testing SEO Automation System..."
echo "================================"

# Test 1: Check if sitemap.xml is accessible
echo "Test 1: Testing sitemap.xml endpoint..."
curl -s -o /tmp/sitemap.xml http://localhost:8000/sitemap.xml || echo "❌ Failed to fetch sitemap.xml"

if [ -f "/tmp/sitemap.xml" ]; then
    if grep -q "<urlset" /tmp/sitemap.xml && grep -q "<?xml" /tmp/sitemap.xml; then
        echo "✅ Sitemap XML is valid and accessible"
    else
        echo "❌ Sitemap XML is invalid"
    fi
fi

# Test 2: Check if RSS feed is accessible
echo -e "\nTest 2: Testing RSS feed endpoint..."
curl -s -o /tmp/rss.xml http://localhost:8000/rss.xml || echo "❌ Failed to fetch RSS feed"

if [ -f "/tmp/rss.xml" ]; then
    if grep -q "<rss" /tmp/rss.xml && grep -q "<?xml" /tmp/rss.xml; then
        echo "✅ RSS feed is valid and accessible"
    else
        echo "❌ RSS feed is invalid"
    fi
fi

# Test 3: Check if robots.txt is accessible
echo -e "\nTest 3: Testing robots.txt endpoint..."
curl -s -o /tmp/robots.txt http://localhost:8000/robots.txt || echo "❌ Failed to fetch robots.txt"

if [ -f "/tmp/robots.txt" ]; then
    if grep -q "Sitemap:" /tmp/robots.txt && grep -q "User-agent:" /tmp/robots.txt; then
        echo "✅ Robots.txt is valid and accessible"
    else
        echo "❌ Robots.txt is invalid"
    fi
fi

# Test 4: Check meta tags on homepage
echo -e "\nTest 4: Testing meta tags on homepage..."
curl -s -o /tmp/homepage.html http://localhost:8000/ || echo "❌ Failed to fetch homepage"

if [ -f "/tmp/homepage.html" ]; then
    if grep -q "<meta name=\"description\"" /tmp/homepage.html && \
       grep -q "<meta property=\"og:title\"" /tmp/homepage.html && \
       grep -q "<meta name=\"twitter:card\"" /tmp/homepage.html; then
        echo "✅ Meta tags are present on homepage"
    else
        echo "❌ Meta tags are missing from homepage"
    fi
fi

# Test 5: Check CLI commands
echo -e "\nTest 5: Testing CLI commands..."

echo "Testing sitemap generation..."
php commands/sitemap.php generate > /tmp/sitemap_test.log 2>&1
if [ $? -eq 0 ] && grep -q "successfully" /tmp/sitemap_test.log; then
    echo "✅ Sitemap CLI command works"
else
    echo "❌ Sitemap CLI command failed"
    cat /tmp/sitemap_test.log
fi

echo "Testing RSS generation..."
php commands/rss.php generate > /tmp/rss_test.log 2>&1
if [ $? -eq 0 ] && grep -q "successfully" /tmp/rss_test.log; then
    echo "✅ RSS CLI command works"
else
    echo "❌ RSS CLI command failed"
    cat /tmp/rss_test.log
fi

# Test 6: Check PHP syntax
echo -e "\nTest 6: Testing PHP syntax..."
php -l src/Services/MetaService.php > /dev/null 2>&1 && echo "✅ MetaService syntax is valid" || echo "❌ MetaService has syntax errors"
php -l src/Services/SitemapService.php > /dev/null 2>&1 && echo "✅ SitemapService syntax is valid" || echo "❌ SitemapService has syntax errors"
php -l src/Services/RssService.php > /dev/null 2>&1 && echo "✅ RssService syntax is valid" || echo "❌ RssService has syntax errors"

# Cleanup
rm -f /tmp/sitemap.xml /tmp/rss.xml /tmp/robots.txt /tmp/homepage.html
rm -f /tmp/sitemap_test.log /tmp/rss_test.log

echo -e "\nSEO Automation System Tests Complete!"
echo "======================================"