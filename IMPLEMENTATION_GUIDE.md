# Public Layout Implementation Guide

## Quick Start

This guide will help you integrate the public layout into your Laravel application or use it as a standalone HTML template.

## Table of Contents

1. [Installation](#installation)
2. [Laravel Integration](#laravel-integration)
3. [Standalone HTML Usage](#standalone-html-usage)
4. [Theme Customization](#theme-customization)
5. [Adding New Pages](#adding-new-pages)
6. [JavaScript API](#javascript-api)
7. [Testing](#testing)

## Installation

### Prerequisites

- Node.js and npm (for SCSS compilation)
- Laravel 8+ (for Laravel integration)
- Modern browser with JavaScript enabled

### Setup Steps

1. **Install Sass globally** (if not already installed):
   ```bash
   npm install -g sass
   ```

2. **Compile SCSS to CSS**:
   ```bash
   npm run sass
   ```

3. **For development with auto-watch**:
   ```bash
   npm run sass:watch
   ```

4. **For production (minified)**:
   ```bash
   npm run sass:prod
   ```

## Laravel Integration

### Step 1: Set Up Routes

Add routes to your `routes/web.php`:

```php
Route::get('/', function () {
    return view('public.home');
})->name('home');

Route::get('/features', function () {
    return view('public.features');
})->name('features');

Route::get('/about', function () {
    return view('public.about');
})->name('about');
```

### Step 2: Configure Assets

Ensure the CSS and JS files are accessible:

```php
// In config/app.php or your asset configuration
'asset_url' => env('ASSET_URL', '/'),
```

### Step 3: Add Helper for CSRF Token

The layout uses `csrf_token()`. Ensure it's available:

```php
// In app/Providers/AppServiceProvider.php
use Illuminate\Support\Facades\Blade;

public function boot()
{
    Blade::if('guest', function () {
        return auth()->guest();
    });
    
    Blade::if('auth', function () {
        return auth()->check();
    });
}
```

### Step 4: Set Up Middleware (Optional)

For authenticated routes, apply middleware:

```php
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
});
```

### Step 5: Configure Locale

Set your application locale in `config/app.php`:

```php
'locale' => 'zh_CN', // or 'en'
'fallback_locale' => 'en',
```

## Standalone HTML Usage

### Step 1: Copy Files

Copy these files to your web server:
- `demo.html` (rename to `index.html`)
- `public/css/public.css`
- `public/js/public.js`

### Step 2: Update Paths

If your directory structure is different, update the asset paths in HTML:

```html
<link rel="stylesheet" href="path/to/public.css">
<script src="path/to/public.js"></script>
```

### Step 3: Test

Open `index.html` in a modern browser. All features should work including:
- Theme switching
- Mobile menu
- Scroll buttons
- Responsive layout

## Theme Customization

### Changing Colors

Edit `resources/scss/public/_variables.scss`:

```scss
:root[data-theme="light"] {
  --color-primary: #your-color;
  --color-bg: #your-bg;
  // ... more variables
}
```

Then recompile:
```bash
npm run sass
```

### Custom Breakpoints

Modify breakpoint variables in `_variables.scss`:

```scss
$breakpoint-sm: 640px;
$breakpoint-md: 768px;
$breakpoint-lg: 1024px;
```

### Custom Fonts

Replace the Inter font with your preferred font:

```html
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=YourFont:wght@300;400;500;600;700&display=swap">
```

Update the font variable:
```scss
$font-family-base: 'YourFont', sans-serif;
```

## Adding New Pages

### Laravel Blade Template

Create a new view file (e.g., `resources/views/public/contact.blade.php`):

```blade
@extends('layouts.public')

@section('title', 'Contact Us')
@section('description', 'Get in touch with us')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Contact Us</h1>
    </div>
    
    <section class="content-section">
        <!-- Your content here -->
    </section>
@endsection

@push('styles')
<style>
    /* Page-specific styles */
</style>
@endpush

@push('scripts')
<script>
    // Page-specific scripts
</script>
@endpush
```

Add the route:
```php
Route::get('/contact', function () {
    return view('public.contact');
})->name('contact');
```

Update navigation in `layouts/public.blade.php`:
```html
<li role="none">
    <a href="{{ url('/contact') }}" class="nav-link" data-pjax role="menuitem">
        <span>Contact</span>
    </a>
</li>
```

### Standalone HTML Page

Copy `demo.html` to a new file (e.g., `contact.html`) and update the content section.

## JavaScript API

### Available Methods

```javascript
// Manually trigger theme change
const themeToggle = document.querySelector('.theme-toggle');
themeToggle.click();

// Update active navigation (after dynamic content load)
PublicLayout.updateActiveNavLink();

// Reinitialize all components
PublicLayout.init();

// Access individual initialization methods
PublicLayout.initThemeToggle();
PublicLayout.initPJAX();
PublicLayout.initScrollControls();
PublicLayout.initMobileMenu();
PublicLayout.initAccessibility();
```

### Custom Event Handlers

Add custom logic to PJAX events:

```javascript
$(document).on('pjax:success', function() {
    // Custom logic after page load
    console.log('Page loaded successfully');
    
    // Re-initialize any plugins
    initializeCustomPlugins();
});

$(document).on('pjax:error', function(xhr, textStatus, error) {
    // Custom error handling
    console.error('Navigation failed:', error);
});
```

### Theme Override Control

```javascript
// Check if theme override is active
const isOverridden = localStorage.getItem('themeOverride') === 'true';

// Clear theme override (allow auto-switching)
localStorage.removeItem('themeOverride');

// Force a specific theme
localStorage.setItem('theme', 'dark');
localStorage.setItem('themeOverride', 'true');
document.documentElement.setAttribute('data-theme', 'dark');
```

## Testing

### Manual Testing Checklist

#### Responsive Design
- [ ] Test on mobile (< 640px)
- [ ] Test on tablet (768px - 1024px)
- [ ] Test on desktop (> 1024px)
- [ ] Mobile menu works correctly
- [ ] All content is readable at each breakpoint

#### Theme Switching
- [ ] Theme toggles on button click
- [ ] Theme persists across page reloads
- [ ] Theme persists across PJAX navigation
- [ ] Auto-switching works based on time
- [ ] Manual override works correctly

#### PJAX Navigation
- [ ] Links with `data-pjax` load via AJAX
- [ ] Loading indicator appears
- [ ] URL updates correctly
- [ ] Browser back/forward buttons work
- [ ] Falls back to full reload on error

#### Scroll Controls
- [ ] Scroll to top appears after scrolling down
- [ ] Scroll to bottom appears when content extends below
- [ ] Smooth scrolling animation works
- [ ] Buttons hide when not needed
- [ ] Keyboard navigation works (Enter/Space)

#### Accessibility
- [ ] Skip link works
- [ ] All interactive elements are keyboard accessible
- [ ] Focus indicators are visible
- [ ] ARIA labels are present
- [ ] Screen reader announces changes correctly

### Browser Testing

Test in these browsers:
- Chrome/Edge (latest 2 versions)
- Firefox (latest 2 versions)
- Safari (latest 2 versions)
- Mobile Safari (iOS 12+)
- Chrome Android (latest)

### Performance Testing

Run Lighthouse audits:
```bash
# Using Chrome DevTools
# Open DevTools > Lighthouse > Generate report
```

Target scores:
- Performance: 90+
- Accessibility: 95+
- Best Practices: 90+
- SEO: 90+

### Automated Testing (Optional)

Create tests using your preferred framework:

```javascript
// Example using Jest
describe('PublicLayout', () => {
    test('theme toggle changes data-theme attribute', () => {
        const toggle = document.querySelector('.theme-toggle');
        const currentTheme = document.documentElement.getAttribute('data-theme');
        
        toggle.click();
        
        const newTheme = document.documentElement.getAttribute('data-theme');
        expect(newTheme).not.toBe(currentTheme);
    });
});
```

## Troubleshooting

### Issue: Styles not loading

**Solution:**
1. Verify CSS file exists: `public/css/public.css`
2. Check file path in HTML/Blade template
3. Clear browser cache
4. Recompile SCSS: `npm run sass`

### Issue: PJAX not working

**Solution:**
1. Ensure jQuery loads before PJAX
2. Check `data-pjax` attribute on links
3. Verify `#pjax-container` exists
4. Check browser console for errors

### Issue: Mobile menu not closing

**Solution:**
1. Check JavaScript console for errors
2. Verify event listeners are attached
3. Test on actual device (not just emulator)
4. Check if CSS transitions are working

### Issue: Theme not persisting

**Solution:**
1. Check if localStorage is enabled
2. Verify JavaScript is not blocked
3. Check for console errors
4. Clear localStorage and try again

### Issue: Scroll buttons not appearing

**Solution:**
1. Ensure page has enough content to scroll
2. Check if JavaScript initialized
3. Verify CSS classes are correct
4. Check z-index conflicts

## Best Practices

### Performance
- Keep JavaScript minimal and efficient
- Use CSS transforms for animations
- Lazy load images and heavy content
- Minimize HTTP requests

### Accessibility
- Always provide ARIA labels
- Ensure keyboard navigation works
- Test with screen readers
- Maintain sufficient color contrast

### SEO
- Use semantic HTML elements
- Add meta descriptions to pages
- Optimize images with alt text
- Implement proper heading hierarchy

### Maintenance
- Keep dependencies updated
- Document any customizations
- Test after updates
- Version control all changes

## Support

For additional help:
- Documentation: See `PUBLIC_LAYOUT_DOCS.md`
- Issues: Check browser console for errors
- Contact: info@example.com

---

**Last Updated**: 2024
**Version**: 1.0.0
