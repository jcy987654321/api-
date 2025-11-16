# Public Layout Quick Reference

Quick reference guide for common tasks and modifications.

## 🚀 Getting Started

### Compile SCSS
```bash
npm run sass
```

### Watch for Changes
```bash
npm run sass:watch
```

### Production Build
```bash
npm run sass:prod
```

## 📝 Creating a New Page

### 1. Create Blade View
```blade
<!-- resources/views/public/mypage.blade.php -->
@extends('layouts.public')

@section('title', 'My Page')

@section('content')
    <h1>My Content</h1>
@endsection
```

### 2. Add Route (Laravel)
```php
// routes/web.php
Route::get('/mypage', function () {
    return view('public.mypage');
});
```

### 3. Add to Navigation
```html
<!-- In resources/views/layouts/public.blade.php -->
<li role="none">
    <a href="{{ url('/mypage') }}" class="nav-link" data-pjax role="menuitem">
        <span>My Page</span>
    </a>
</li>
```

## 🎨 Customizing Colors

### Light Theme Colors
```scss
// In resources/scss/public/_variables.scss
:root[data-theme="light"] {
  --color-primary: #YOUR_COLOR;
  --color-bg: #YOUR_BG;
}
```

### Dark Theme Colors
```scss
:root[data-theme="dark"] {
  --color-primary: #YOUR_COLOR;
  --color-bg: #YOUR_BG;
}
```

**Don't forget to recompile:** `npm run sass`

## 📱 Responsive Breakpoints

### Mobile Only
```scss
@media (max-width: 640px) {
  // Mobile styles
}
```

### Tablet and Up
```scss
@media (min-width: 768px) {
  // Tablet+ styles
}
```

### Desktop and Up
```scss
@media (min-width: 1024px) {
  // Desktop+ styles
}
```

## 🎯 Common Components

### Button
```html
<a href="#" class="btn btn-primary">Primary Button</a>
<a href="#" class="btn btn-outline">Outline Button</a>
<a href="#" class="btn btn-primary btn-lg">Large Button</a>
```

### Card
```html
<div class="card">
    <h3>Card Title</h3>
    <p>Card content...</p>
</div>
```

### Feature Card
```html
<article class="feature-card">
    <div class="feature-icon">🚀</div>
    <h3 class="feature-title">Title</h3>
    <p class="feature-description">Description</p>
</article>
```

### Grid Layout
```html
<div class="features-grid">
    <!-- Cards automatically arrange in responsive grid -->
</div>
```

## 🔧 JavaScript API

### Access Layout Object
```javascript
// The PublicLayout object is globally available
PublicLayout.init(); // Reinitialize all components
```

### Theme Control
```javascript
// Get current theme
const theme = document.documentElement.getAttribute('data-theme');

// Set theme programmatically
document.documentElement.setAttribute('data-theme', 'dark');
localStorage.setItem('theme', 'dark');
localStorage.setItem('themeOverride', 'true');
```

### PJAX Events
```javascript
// After successful PJAX load
$(document).on('pjax:success', function() {
    console.log('Page loaded!');
});

// On PJAX error
$(document).on('pjax:error', function(xhr, textStatus, error) {
    console.error('Error:', error);
});
```

### Update Active Nav Link
```javascript
PublicLayout.updateActiveNavLink();
```

## 📐 Spacing Utilities

### Margins
```html
<div class="mt-sm">Margin top small</div>
<div class="mt-md">Margin top medium</div>
<div class="mt-lg">Margin top large</div>
<div class="mt-xl">Margin top extra large</div>

<!-- Also available: mb-* (bottom), ml-* (left), mr-* (right) -->
```

### Padding
```html
<div class="pt-sm">Padding top small</div>
<div class="pt-md">Padding top medium</div>
<div class="pt-lg">Padding top large</div>
<div class="pt-xl">Padding top extra large</div>

<!-- Also available: pb-* (bottom), pl-* (left), pr-* (right) -->
```

## 🎭 Blade Directives

### Content Sections
```blade
@section('content')
    <!-- Main content -->
@endsection

@section('hero')
    <!-- Hero content (optional) -->
@endsection
```

### Custom Styles/Scripts
```blade
@push('styles')
<style>
    /* Page-specific CSS */
</style>
@endpush

@push('scripts')
<script>
    // Page-specific JS
</script>
@endpush
```

### Authentication
```blade
@guest
    <!-- Show to guests only -->
@endguest

@auth
    <!-- Show to authenticated users only -->
@endauth
```

## 🔍 SEO Meta Tags

```blade
@section('title', 'Page Title')
@section('description', 'Page description for search engines')
@section('keywords', 'keyword1, keyword2, keyword3')
```

## ♿ Accessibility Attributes

### Links
```html
<a href="#" aria-label="Descriptive label">Link</a>
```

### Buttons
```html
<button type="button" aria-label="Button purpose">
    Button
</button>
```

### Navigation
```html
<nav role="navigation" aria-label="Main navigation">
    <!-- Nav content -->
</nav>
```

### Landmarks
```html
<header role="banner">...</header>
<main role="main">...</main>
<footer role="contentinfo">...</footer>
```

## 🎨 CSS Variables Reference

### Colors
```css
var(--color-primary)
var(--color-primary-hover)
var(--color-bg)
var(--color-bg-secondary)
var(--color-text)
var(--color-text-secondary)
var(--color-border)
var(--color-surface)
```

### Shadows
```css
var(--shadow-sm)
var(--shadow-md)
var(--shadow-lg)
var(--shadow-xl)
```

## 🐛 Common Issues

### SCSS Not Compiling
```bash
# Install sass globally
npm install -g sass

# Then compile
npm run sass
```

### Theme Not Persisting
```javascript
// Check localStorage
console.log(localStorage.getItem('theme'));
console.log(localStorage.getItem('themeOverride'));

// Clear and retry
localStorage.clear();
```

### PJAX Not Working
```javascript
// Check jQuery is loaded
console.log(typeof jQuery);

// Check PJAX container exists
console.log($('#pjax-container').length);

// Check link has data-pjax attribute
<a href="/page" data-pjax>Link</a>
```

### Styles Not Updating
```bash
# Recompile SCSS
npm run sass

# Clear browser cache
# Hard refresh: Ctrl+Shift+R (Windows) or Cmd+Shift+R (Mac)
```

## 📖 Documentation Files

- **README.md** - Project overview
- **PUBLIC_LAYOUT_DOCS.md** - Complete documentation
- **IMPLEMENTATION_GUIDE.md** - Setup guide
- **TESTING_CHECKLIST.md** - Testing checklist
- **IMPLEMENTATION_SUMMARY.md** - Implementation summary
- **QUICK_REFERENCE.md** - This file

## 🔗 External Dependencies

### CDN Links
```html
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- PJAX -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.pjax/2.0.1/jquery.pjax.min.js"></script>

<!-- Inter Font -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
```

## ⚡ Performance Tips

1. **Minimize HTTP requests** - Combine files when possible
2. **Use CDN** for common libraries
3. **Enable caching** in production
4. **Compress assets** - Use `npm run sass:prod`
5. **Lazy load images** - Add `loading="lazy"` to images
6. **Debounce scroll events** - Already implemented
7. **Use CSS transforms** for animations - Already implemented

## 🎯 Best Practices

1. **Always use data-pjax** on internal links for smooth navigation
2. **Include ARIA labels** on all interactive elements
3. **Test on real devices** not just browser DevTools
4. **Validate HTML** using W3C validator
5. **Check color contrast** using browser DevTools
6. **Test keyboard navigation** regularly
7. **Keep dependencies updated** but test thoroughly
8. **Document custom modifications** for future reference

## 📞 Support

- **Email:** info@example.com
- **Phone:** +86 123 456 7890

---

**Quick Reference Version:** 1.0.0
**Last Updated:** November 2024
