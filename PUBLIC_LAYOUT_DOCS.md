# Public Layout Documentation

## Overview

This public-facing layout implementation provides a comprehensive, responsive, and accessible foundation for the management system's public pages. It includes dark/light theme switching, PJAX navigation, scroll controls, and modern UI/UX features.

## Features Implemented

### 1. Responsive Layout
- **Mobile-first design** with breakpoints for mobile (640px), tablet (768px), desktop (1024px), and large screens (1280px+)
- **Collapsible navigation** with hamburger menu for mobile devices
- **Touch-friendly controls** optimized for mobile interactions
- **Flexible grid systems** that adapt to different screen sizes

### 2. Dark/Light Theme System
- **Automatic theme switching** based on local time (6 AM - 6 PM = light theme, otherwise dark)
- **Manual override** with persistent storage in localStorage
- **Smooth transitions** between themes using CSS variables
- **Theme persistence** across PJAX navigation
- **Graceful animations** with respect for prefers-reduced-motion

### 3. PJAX Navigation
- **Partial page updates** using jquery-pjax for smoother navigation
- **Loading indicators** with animated progress bar
- **Fallback handling** for errors and timeouts
- **Full page reload** on PJAX failures
- **Active link highlighting** based on current URL
- **Browser history support** with push/pop state

### 4. Scroll Controls
- **Scroll to top** button appears after scrolling 300px
- **Scroll to bottom** button appears when content extends below fold
- **Smooth scrolling** with custom easing function
- **Keyboard accessible** with Enter and Space key support
- **Auto-hide/show** based on scroll position
- **Animated transitions** for visibility changes

### 5. Accessibility Features
- **Skip to main content** link for screen readers
- **ARIA labels and roles** throughout the layout
- **Focus states** for all interactive elements
- **Keyboard navigation** support
- **Semantic HTML5** elements (header, nav, main, footer, article, section)
- **Color contrast** meeting WCAG 2.1 AA standards
- **Reduced motion support** for users with vestibular disorders

### 6. Performance Optimizations
- **CSS variables** for dynamic theming without JavaScript recalculation
- **Preload critical assets** (fonts)
- **Debounced scroll handlers** to prevent performance issues
- **Efficient DOM queries** cached where possible
- **Minimal JavaScript** for core functionality

## File Structure

```
project/
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   └── public.blade.php          # Master layout
│   │   └── public/
│   │       ├── home.blade.php            # Homepage
│   │       ├── features.blade.php        # Features page
│   │       └── about.blade.php           # About page
│   └── scss/
│       └── public/
│           ├── _variables.scss           # Color palettes, spacing, typography
│           ├── _base.scss                # Base styles and resets
│           ├── _components.scss          # Component styles
│           └── styles.scss               # Main entry point
├── public/
│   ├── css/
│   │   ├── public.css                    # Compiled CSS
│   │   └── public.css.map                # Source map
│   └── js/
│       └── public.js                     # Main JavaScript
└── PUBLIC_LAYOUT_DOCS.md                 # This file
```

## Usage

### Extending the Layout

Create a new Blade view and extend the public layout:

```blade
@extends('layouts.public')

@section('title', 'Page Title')
@section('description', 'Page description for SEO')

@section('hero')
    <!-- Optional hero content -->
@endsection

@section('content')
    <!-- Main page content -->
@endsection

@push('styles')
    <!-- Additional page-specific styles -->
@endpush

@push('scripts')
    <!-- Additional page-specific scripts -->
@endpush
```

### Theme Customization

Modify color palettes in `resources/scss/public/_variables.scss`:

```scss
:root[data-theme="light"] {
  --color-primary: #3b82f6;
  --color-bg: #ffffff;
  // ... more variables
}

:root[data-theme="dark"] {
  --color-primary: #60a5fa;
  --color-bg: #0f172a;
  // ... more variables
}
```

### PJAX Configuration

Add `data-pjax` attribute to links for PJAX navigation:

```html
<a href="/features" data-pjax>Features</a>
```

Links without `data-pjax` will use traditional full-page navigation.

### Compiling SCSS

To recompile SCSS after making changes:

```bash
sass resources/scss/public/styles.scss public/css/public.css
```

For development with auto-watch:

```bash
sass --watch resources/scss/public/styles.scss public/css/public.css
```

## Browser Support

- **Chrome/Edge**: Latest 2 versions
- **Firefox**: Latest 2 versions
- **Safari**: Latest 2 versions
- **Mobile browsers**: iOS Safari 12+, Chrome Android 90+

## Accessibility Compliance

- **WCAG 2.1 Level AA** compliant
- **Keyboard navigation** fully supported
- **Screen reader** optimized
- **Color contrast ratios** meet minimum requirements
- **Focus indicators** visible and clear

## JavaScript API

The layout exposes a global `PublicLayout` object with the following methods:

```javascript
// Manually update active navigation link
PublicLayout.updateActiveNavLink();

// Reinitialize components (useful after dynamic content changes)
PublicLayout.init();
```

## Theme Auto-Switch Schedule

The automatic theme switching follows this schedule:

- **6:00 AM - 5:59 PM**: Light theme
- **6:00 PM - 5:59 AM**: Dark theme

Users can override this by clicking the theme toggle button. The override persists across page loads until the user clicks the toggle again.

## Dependencies

- **jQuery 3.6.0**: Required for PJAX
- **jquery-pjax 2.0.1**: Handles partial page updates
- **Sass/SCSS**: For styling compilation
- **Inter font**: Modern sans-serif typeface

## Testing Recommendations

1. **Responsive Testing**: Test on actual devices or using browser dev tools
2. **Theme Testing**: Verify both themes and transitions
3. **PJAX Testing**: Navigate between pages and check for errors
4. **Keyboard Testing**: Tab through all interactive elements
5. **Screen Reader Testing**: Use NVDA, JAWS, or VoiceOver
6. **Performance Testing**: Check Lighthouse scores

## Future Enhancements

Potential improvements for future iterations:

- Service Worker for offline support
- Progressive Web App (PWA) features
- Advanced animations with Intersection Observer
- Lazy loading for images and content
- Client-side caching strategies
- A/B testing framework integration

## Troubleshooting

### PJAX not working
- Ensure jQuery is loaded before PJAX script
- Check that `data-pjax` attribute is present on links
- Verify `#pjax-container` exists in layout

### Theme not persisting
- Check browser localStorage is enabled
- Verify JavaScript is not blocked
- Check browser console for errors

### Styles not loading
- Ensure CSS file is compiled from SCSS
- Check file path in layout matches actual location
- Clear browser cache

### Mobile menu not closing
- Check JavaScript console for errors
- Verify event listeners are attached
- Test on actual device vs. emulator

## Support

For issues or questions:
- Email: info@example.com
- Phone: +86 123 456 7890

---

**Last Updated**: 2024
**Version**: 1.0.0
**Author**: Development Team
