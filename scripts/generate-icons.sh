#!/bin/bash

# Icon Generation Script
# Generates favicons and app icons from a source image

set -e

SOURCE="${1:-resources/images/logo.png}"
OUTPUT_DIR="public"

if [ ! -f "$SOURCE" ]; then
    echo "Error: Source image not found: $SOURCE"
    echo "Usage: $0 <source-image>"
    exit 1
fi

echo "Generating icons from: $SOURCE"

# Create output directory if it doesn't exist
mkdir -p "$OUTPUT_DIR"

# Favicons
echo "Generating favicons..."
convert "$SOURCE" -resize 16x16 "$OUTPUT_DIR/favicon-16x16.png"
convert "$SOURCE" -resize 32x32 "$OUTPUT_DIR/favicon-32x32.png"
convert "$SOURCE" -resize 48x48 "$OUTPUT_DIR/favicon-48x48.png"

# Convert to .ico (multiple sizes in one file)
convert "$SOURCE" -resize 16x16 \
        "$SOURCE" -resize 32x32 \
        "$SOURCE" -resize 48x48 \
        "$OUTPUT_DIR/favicon.ico"

# Apple Touch Icons
echo "Generating Apple Touch Icons..."
convert "$SOURCE" -resize 180x180 "$OUTPUT_DIR/apple-touch-icon.png"
convert "$SOURCE" -resize 152x152 "$OUTPUT_DIR/apple-touch-icon-152x152.png"
convert "$SOURCE" -resize 167x167 "$OUTPUT_DIR/apple-touch-icon-167x167.png"
convert "$SOURCE" -resize 180x180 "$OUTPUT_DIR/apple-touch-icon-180x180.png"

# Android Chrome Icons
echo "Generating Android Chrome Icons..."
convert "$SOURCE" -resize 192x192 "$OUTPUT_DIR/android-chrome-192x192.png"
convert "$SOURCE" -resize 512x512 "$OUTPUT_DIR/android-chrome-512x512.png"

# Microsoft Tiles
echo "Generating Microsoft Tiles..."
convert "$SOURCE" -resize 144x144 "$OUTPUT_DIR/mstile-144x144.png"
convert "$SOURCE" -resize 150x150 "$OUTPUT_DIR/mstile-150x150.png"
convert "$SOURCE" -resize 310x310 "$OUTPUT_DIR/mstile-310x310.png"

# Safari Pinned Tab (SVG recommended, but PNG fallback)
echo "Generating Safari Pinned Tab..."
convert "$SOURCE" -resize 128x128 "$OUTPUT_DIR/safari-pinned-tab.png"

# Generate manifest.json for PWA
echo "Generating web app manifest..."
cat > "$OUTPUT_DIR/site.webmanifest" << EOF
{
  "name": "API Management System",
  "short_name": "API System",
  "description": "强大的管理系统 - Powerful Management System",
  "icons": [
    {
      "src": "/android-chrome-192x192.png",
      "sizes": "192x192",
      "type": "image/png"
    },
    {
      "src": "/android-chrome-512x512.png",
      "sizes": "512x512",
      "type": "image/png"
    }
  ],
  "theme_color": "#007bff",
  "background_color": "#ffffff",
  "display": "standalone",
  "orientation": "portrait",
  "start_url": "/"
}
EOF

# Generate browserconfig.xml for Microsoft
echo "Generating browser config..."
cat > "$OUTPUT_DIR/browserconfig.xml" << EOF
<?xml version="1.0" encoding="utf-8"?>
<browserconfig>
  <msapplication>
    <tile>
      <square150x150logo src="/mstile-150x150.png"/>
      <square310x310logo src="/mstile-310x310.png"/>
      <TileColor>#007bff</TileColor>
    </tile>
  </msapplication>
</browserconfig>
EOF

echo ""
echo "✓ Icon generation complete!"
echo ""
echo "Add these lines to your HTML <head>:"
echo ""
cat << EOF
<!-- Favicons -->
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="shortcut icon" href="/favicon.ico">

<!-- Apple Touch Icons -->
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">

<!-- Android Chrome -->
<link rel="manifest" href="/site.webmanifest">

<!-- Microsoft -->
<meta name="msapplication-TileColor" content="#007bff">
<meta name="msapplication-config" content="/browserconfig.xml">

<!-- Safari -->
<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#007bff">

<!-- Theme Color -->
<meta name="theme-color" content="#007bff">
EOF
echo ""
