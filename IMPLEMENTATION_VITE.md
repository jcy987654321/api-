# Vite Asset Pipeline Implementation Guide

This guide provides step-by-step instructions for implementing and using the Vite-based asset pipeline in your Laravel application.

## Table of Contents

1. [Overview](#overview)
2. [Installation](#installation)
3. [Configuration](#configuration)
4. [Development Workflow](#development-workflow)
5. [Production Deployment](#production-deployment)
6. [Laravel Integration](#laravel-integration)
7. [Using Lazy-Loaded Modules](#using-lazy-loaded-modules)
8. [Troubleshooting](#troubleshooting)

## Overview

The Vite asset pipeline provides:
- Fast development with Hot Module Replacement (HMR)
- Optimized production builds with code splitting
- Automatic cache busting with hashed filenames
- Legacy browser support with polyfills
- Image optimization and responsive images
- Gzip compression
- No CDN dependencies (all assets hosted locally)

## Installation

### Step 1: Install Node.js Dependencies

```bash
npm install
```

This installs all required dependencies from `package.json`:
- Vite and Laravel Vite plugin
- jQuery, PJAX, Chart.js, Prism.js
- PostCSS with Autoprefixer
- Polyfills (core-js, regenerator-runtime)
- Build optimization tools

### Step 2: Verify Installation

```bash
# Check Vite is installed
npx vite --version

# Check all files are present
ls resources/js/app.js
ls resources/scss/public/styles.scss
ls vite.config.js
```

## Configuration

### Vite Configuration

The `vite.config.js` file is pre-configured with:

```javascript
export default defineConfig({
  plugins: [
    laravel({
      input: [
        'resources/scss/public/styles.scss',
        'resources/js/app.js',
      ],
    }),
    legacy({ /* browser polyfills */ }),
    viteCompression({ /* gzip compression */ }),
  ],
  build: {
    outDir: 'public/build',
    manifest: true,
    // Code splitting configuration
  },
  // CSS preprocessing with autoprefixer
  // Path aliases (@js, @scss, @images)
});
```

### PostCSS Configuration

The `postcss.config.js` adds browser prefixes automatically:

```javascript
export default {
  plugins: {
    autoprefixer: {
      overrideBrowserslist: ['last 2 versions', '> 1%', 'iOS >= 10'],
    },
  },
};
```

### Environment Variables

Create a `.env` file (or use `.env.example`):

```env
VITE_APP_NAME="API Management System"
VITE_APP_URL=http://localhost:8000
```

Access in JavaScript:
```javascript
const appName = import.meta.env.VITE_APP_NAME;
```

## Development Workflow

### Starting Development Server

```bash
# Terminal 1: Start Laravel
php artisan serve

# Terminal 2: Start Vite dev server
npm run dev
```

Vite will start on `http://localhost:5173` and proxy assets to your Laravel app.

### Features in Development Mode

1. **Hot Module Replacement (HMR)**
   - Changes to JS/CSS reload instantly
   - No page refresh needed
   - State preserved

2. **Source Maps**
   - Debug original source code
   - Error messages show correct line numbers

3. **Fast Rebuilds**
   - Only changed modules are rebuilt
   - Typically < 100ms

### Making Changes

#### JavaScript Changes

1. Edit files in `resources/js/`:
   ```javascript
   // resources/js/public-layout.js
   console.log('Updated!');
   ```

2. Save file - browser updates automatically via HMR

#### CSS Changes

1. Edit files in `resources/scss/public/`:
   ```scss
   // resources/scss/public/_components.scss
   .btn {
     background: red; // Changed from blue
   }
   ```

2. Save file - styles update without page reload

### Adding New Features

#### Add a New JavaScript Module

1. Create file:
   ```javascript
   // resources/js/my-feature.js
   export default {
     init() {
       console.log('My feature initialized');
     }
   };
   ```

2. Import in `app.js`:
   ```javascript
   // resources/js/app.js
   import MyFeature from './my-feature';
   MyFeature.init();
   ```

3. For lazy loading:
   ```javascript
   // resources/js/app.js
   window.loadMyFeature = () => import('./my-feature');
   
   // Use in your code
   document.getElementById('btn').addEventListener('click', async () => {
     const module = await window.loadMyFeature();
     module.default.init();
   });
   ```

#### Add a New SCSS File

1. Create partial:
   ```scss
   // resources/scss/public/_my-component.scss
   .my-component {
     padding: 1rem;
   }
   ```

2. Import in `styles.scss`:
   ```scss
   // resources/scss/public/styles.scss
   @import 'my-component';
   ```

## Production Deployment

### Step 1: Build Assets

```bash
npm run build
```

This creates optimized assets in `public/build/`:
- Minified JavaScript and CSS
- Hashed filenames (e.g., `app-abc123.js`)
- Code-split chunks
- Gzipped versions
- `manifest.json` for asset mapping

### Step 2: Verify Build

```bash
./scripts/verify-build.sh
```

Checks:
- Build directory exists
- Manifest generated
- Files properly hashed
- Performance budgets met
- No CDN dependencies

### Step 3: Test Production Build Locally

```bash
# Preview built assets
npm run preview

# Or serve with Laravel
php artisan serve
# Visit http://localhost:8000
```

### Step 4: Deploy

#### Option A: Commit Build Directory

```bash
git add public/build
git commit -m "Build assets for production"
git push
```

On server:
```bash
git pull
# Build directory is already included
```

#### Option B: Build on Server

```bash
# On server after deployment
npm install --production
npm run build
```

#### Option C: CI/CD Pipeline

```yaml
# .github/workflows/deploy.yml
- name: Build assets
  run: |
    npm ci
    npm run build
    
- name: Deploy
  run: |
    rsync -avz public/build/ server:/path/to/public/build/
```

### Step 5: Optimize Web Server

#### Nginx Configuration

```nginx
# Cache static assets
location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
    expires 1y;
    add_header Cache-Control "public, immutable";
}

# Serve pre-compressed files
location ~* \.(js|css)$ {
    gzip_static on;
}

# Security headers
add_header X-Content-Type-Options "nosniff";
add_header X-Frame-Options "SAMEORIGIN";
add_header X-XSS-Protection "1; mode=block";
```

#### Apache Configuration

```apache
# .htaccess in public/build/

# Cache static assets
<FilesMatch "\.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$">
    Header set Cache-Control "public, max-age=31536000, immutable"
</FilesMatch>

# Serve pre-compressed files
<IfModule mod_headers.c>
    RewriteCond %{HTTP:Accept-encoding} gzip
    RewriteCond %{REQUEST_FILENAME}\.gz -s
    RewriteRule ^(.*)$ $1\.gz [QSA]
</IfModule>
```

## Laravel Integration

### Blade Templates

The `@vite` directive loads assets:

```blade
{{-- resources/views/layouts/public.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    @vite(['resources/scss/public/styles.scss', 'resources/js/app.js'])
</head>
<body>
    @yield('content')
</body>
</html>
```

### Conditional Loading (Development vs Production)

```blade
@if (file_exists(public_path('build/manifest.json')))
    {{-- Production: Use Vite build --}}
    @vite(['resources/scss/public/styles.scss', 'resources/js/app.js'])
@else
    {{-- Development: Use fallback or show warning --}}
    <link rel="stylesheet" href="{{ asset('css/public.css') }}">
    <script src="{{ asset('js/public.js') }}"></script>
@endif
```

### Asset URL Helper

Create a helper for asset URLs:

```php
// app/Helpers/AssetHelper.php

function vite_asset($path) {
    $manifest = json_decode(
        file_get_contents(public_path('build/manifest.json')),
        true
    );
    
    return asset('build/' . $manifest[$path]['file']);
}
```

Use in Blade:
```blade
<img src="{{ vite_asset('resources/images/logo.png') }}" alt="Logo">
```

### Dynamic Imports in Blade

Load modules conditionally:

```blade
@if ($needsCharts)
    @push('scripts')
    <script>
        // Load Chart.js when needed
        window.initCharts().then(module => {
            const chart = module.createChart('myChart', {
                type: 'bar',
                data: @json($chartData)
            });
        });
    </script>
    @endpush
@endif
```

## Using Lazy-Loaded Modules

### Chart.js

```javascript
// Load Chart.js dynamically
const chartModule = await window.initCharts();

// Create a chart
const chart = chartModule.createChart('myChart', {
  type: 'line',
  data: {
    labels: ['Jan', 'Feb', 'Mar'],
    datasets: [{
      label: 'Sales',
      data: [12, 19, 3]
    }]
  }
});

// Destroy chart when done
chartModule.destroyChart(chart);
```

### Syntax Highlighter

```javascript
// Load Prism.js dynamically
const prism = await window.initSyntaxHighlighter();

// Highlight all code blocks
prism.initSyntaxHighlighting();

// Or highlight specific element
const codeBlock = document.querySelector('pre code');
prism.highlightElement(codeBlock);
```

### Media Preview

```javascript
// Load media preview dynamically
const preview = await window.initMediaPreview();

// Initialize lightbox
preview.init();
```

In HTML:
```html
<a href="full-size.jpg" data-lightbox="gallery">
    <img src="thumbnail.jpg" alt="Photo">
</a>
```

## Troubleshooting

### Common Issues

#### 1. "Vite manifest not found"

**Problem:** Laravel can't find the Vite manifest file.

**Solution:**
```bash
# Build assets first
npm run build

# Or start dev server
npm run dev
```

#### 2. "Module not found"

**Problem:** Import path is incorrect.

**Solution:** Use configured aliases:
```javascript
// Wrong
import MyModule from '../../../js/my-module';

// Right
import MyModule from '@js/my-module';
```

#### 3. HMR not working

**Problem:** Changes don't reflect in browser.

**Solution:**
```bash
# Restart Vite dev server
# Press Ctrl+C to stop
npm run dev

# Hard refresh browser (Ctrl+Shift+R)
```

#### 4. Build fails with memory error

**Problem:** Node runs out of memory.

**Solution:**
```bash
NODE_OPTIONS="--max-old-space-size=4096" npm run build
```

#### 5. Assets not loading in production

**Problem:** 404 errors for assets.

**Solution:**
```bash
# Verify build directory exists
ls public/build/manifest.json

# Check file permissions
chmod -R 755 public/build

# Verify web server configuration
```

#### 6. Styles not applying

**Problem:** CSS changes not taking effect.

**Solution:**
```bash
# Clear browser cache
# Hard refresh (Ctrl+Shift+R)

# Rebuild assets
npm run build

# Check browser console for errors
```

### Debug Mode

Enable verbose logging:

```javascript
// vite.config.js
export default defineConfig({
  logLevel: 'info', // or 'debug'
  // ...
});
```

### Performance Debugging

```bash
# Run Lighthouse audit
npm run lighthouse:mobile

# Analyze bundle sizes
npm run build -- --mode development

# Check build stats
ls -lh public/build/assets/
```

## Best Practices

### 1. Code Splitting

Split large libraries into separate chunks:

```javascript
// Lazy load heavy modules
const ChartJS = () => import('chart.js');
const Prism = () => import('prismjs');
```

### 2. Image Optimization

Use WebP with fallbacks:

```html
<picture>
  <source srcset="image.webp" type="image/webp">
  <img src="image.jpg" alt="Description">
</picture>
```

### 3. Lazy Loading Images

```html
<img data-src="large-image.jpg" loading="lazy" alt="Description">
```

### 4. Preload Critical Assets

```blade
<link rel="preload" href="{{ vite_asset('resources/js/app.js') }}" as="script">
```

### 5. Environment-Specific Code

```javascript
if (import.meta.env.DEV) {
  console.log('Development mode');
}

if (import.meta.env.PROD) {
  // Production-only code
}
```

### 6. Tree Shaking

Only import what you need:

```javascript
// Bad: Imports entire library
import _ from 'lodash';

// Good: Imports only what's needed
import debounce from 'lodash/debounce';
```

### 7. Cache Management

Use versioned imports for cache busting:

```javascript
// Vite automatically handles this with hashed filenames
// app-abc123.js changes to app-def456.js when content changes
```

## Additional Resources

- [Vite Documentation](https://vitejs.dev/)
- [Laravel Vite Plugin Docs](https://laravel.com/docs/vite)
- [Asset Management Guide](ASSET_MANAGEMENT.md)
- [Image Optimization Guide](IMAGE_OPTIMIZATION.md)
- [Performance Budgets](performance-budget.json)
- [Lighthouse Configuration](lighthouserc.json)

## Support

If you encounter issues:

1. Check this guide and [ASSET_MANAGEMENT.md](ASSET_MANAGEMENT.md)
2. Review [Troubleshooting](#troubleshooting) section
3. Check build output for errors: `npm run build`
4. Verify browser console for runtime errors
5. Run verification script: `./scripts/verify-build.sh`

For additional help, contact the development team.
