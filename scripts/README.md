# Build & Optimization Scripts

This directory contains utility scripts for building, optimizing, and verifying assets.

## Available Scripts

### generate-icons.sh

Generates favicons and app icons from a source image.

**Requirements:**
- ImageMagick (`convert` command)

**Usage:**
```bash
./scripts/generate-icons.sh resources/images/logo.png
```

**Output:**
- `public/favicon.ico`
- `public/favicon-*.png`
- `public/apple-touch-icon*.png`
- `public/android-chrome-*.png`
- `public/mstile-*.png`
- `public/site.webmanifest`
- `public/browserconfig.xml`

### verify-build.sh

Verifies that the production build meets all requirements.

**Usage:**
```bash
# After running npm run build
./scripts/verify-build.sh
```

**Checks:**
- Build directory exists
- Manifest file generated
- CSS and JS assets present
- Files have cache-busting hashes
- Gzip compression enabled
- Code splitting working
- No source maps in production
- No CDN dependencies
- Performance budgets met

## Installation

### macOS

```bash
# Install ImageMagick
brew install imagemagick

# Install image optimization tools
brew install jpegoptim optipng pngquant webp
```

### Ubuntu/Debian

```bash
# Install ImageMagick
sudo apt-get install imagemagick

# Install image optimization tools
sudo apt-get install jpegoptim optipng pngquant webp
```

### Windows

Download and install:
- [ImageMagick](https://imagemagick.org/script/download.php#windows)
- Use Git Bash or WSL to run scripts
