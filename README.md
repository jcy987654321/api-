# API Management System
强大的管理系统 (Powerful Management System)

A modern, responsive public-facing layout with dark/light themes, PJAX navigation, and comprehensive accessibility features.

## Features

- 🎨 **Responsive Design**: Mobile-first layout adapting to all screen sizes
- 🌓 **Smart Theme System**: Auto-switching between light/dark themes based on time of day
- ⚡ **PJAX Navigation**: Smooth page transitions without full reloads
- ♿ **Accessibility**: WCAG 2.1 AA compliant with full keyboard navigation
- 📜 **Scroll Controls**: Smart scroll-to-top/bottom buttons
- 🎯 **SEO Optimized**: Semantic HTML and proper meta tags
- 🚀 **Performance**: Optimized assets with Vite, code splitting, and lazy loading
- 📦 **Modern Build Pipeline**: Vite-powered with HMR, cache busting, and compression
- 🖼️ **Image Optimization**: WebP support with responsive images and lazy loading
- 📊 **Charts & Visualization**: Lazy-loaded Chart.js for data visualization
- 💻 **Syntax Highlighting**: Code highlighting with Prism.js (lazy-loaded)
- 🔍 **Media Preview**: Lightbox for images and videos with keyboard navigation

## Quick Start

### Prerequisites

- Node.js 18+ and npm (for asset compilation)
- Modern browser with JavaScript enabled

### Installation

1. Clone the repository:
   ```bash
   git clone <repository-url>
   cd <project-directory>
   ```

2. Install dependencies:
   ```bash
   npm install
   ```

3. For development (with hot module replacement):
   ```bash
   npm run dev
   ```

4. For production build:
   ```bash
   npm run build
   ```

5. Open `demo.html` in your browser to see the layout in action

### For Laravel Integration

See the [Implementation Guide](IMPLEMENTATION_GUIDE.md) for detailed Laravel integration steps.

## Documentation

- **[Asset Management](ASSET_MANAGEMENT.md)**: Complete guide to the Vite build pipeline and asset workflow
- **[Image Optimization](IMAGE_OPTIMIZATION.md)**: Best practices for image optimization and responsive images
- **[Implementation Guide](IMPLEMENTATION_GUIDE.md)**: Step-by-step setup and integration
- **[Public Layout Documentation](PUBLIC_LAYOUT_DOCS.md)**: Complete feature documentation and API reference
- **[Performance Guide](lighthouserc.json)**: Lighthouse configuration and performance budgets

## Project Structure

```
project/
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   └── public.blade.php          # Master Blade layout (Vite-enabled)
│   │   └── public/
│   │       ├── home.blade.php            # Homepage
│   │       ├── features.blade.php        # Features page
│   │       └── about.blade.php           # About page
│   ├── scss/
│   │   └── public/
│   │       ├── _variables.scss           # Design tokens
│   │       ├── _base.scss                # Base styles
│   │       ├── _components.scss          # Component styles
│   │       ├── _media-preview.scss       # Media preview styles
│   │       └── styles.scss               # Main SCSS entry point
│   ├── js/
│   │   ├── app.js                        # Main JS entry point
│   │   ├── vendor.js                     # jQuery & PJAX
│   │   ├── polyfills.js                  # Browser polyfills
│   │   ├── public-layout.js              # Layout functionality
│   │   ├── chart-loader.js               # Lazy-loaded Chart.js
│   │   ├── syntax-highlighter.js         # Lazy-loaded Prism.js
│   │   └── media-preview.js              # Lazy-loaded media preview
│   └── images/                           # Image assets
├── public/
│   ├── build/                            # Vite build output (gitignored)
│   │   ├── manifest.json                 # Asset manifest
│   │   └── assets/                       # Hashed CSS/JS files
│   ├── css/                              # Fallback CSS
│   └── js/                               # Fallback JS
├── scripts/
│   ├── generate-icons.sh                 # Icon generation
│   └── verify-build.sh                   # Build verification
├── vite.config.js                        # Vite configuration
├── postcss.config.js                     # PostCSS configuration
├── lighthouserc.json                     # Lighthouse CI config
├── performance-budget.json               # Performance budgets
├── demo.html                             # Standalone demo
├── package.json                          # Dependencies & scripts
├── ASSET_MANAGEMENT.md                   # Asset pipeline docs
├── IMAGE_OPTIMIZATION.md                 # Image optimization guide
└── README.md                             # This file
```

## Theme System

The layout features an intelligent theme system:

- **Auto-switching**: Automatically switches between light (6 AM - 6 PM) and dark themes
- **Manual override**: Users can manually toggle theme, with preference saved to localStorage
- **Smooth transitions**: CSS variable-based theming with graceful animations
- **Persistent**: Theme preference persists across PJAX navigation

## Browser Support

Modern browsers with automatic polyfills for legacy browsers:

- Chrome/Edge (latest 2 versions)
- Firefox (latest 2 versions)
- Safari 10+
- iOS Safari 10+
- Chrome Android 90+

**Polyfills included for:**
- fetch API
- Promise
- IntersectionObserver
- Object.assign
- Array methods (find, from, includes)
- Element.closest, Element.matches

## Development

### Available Scripts

```bash
# Development mode with HMR
npm run dev

# Production build (optimized, minified, hashed)
npm run build

# Preview production build
npm run preview

# Run Lighthouse audit (desktop)
npm run lighthouse

# Run Lighthouse audit (mobile)
npm run lighthouse:mobile

# Legacy SCSS compilation (fallback)
npm run sass
npm run sass:watch
npm run sass:prod
```

### Customization

Edit variables in `resources/scss/public/_variables.scss` to customize:
- Color palettes
- Typography
- Spacing
- Breakpoints
- Transitions

Then recompile the SCSS.

## Performance

Built for speed and optimized for Core Web Vitals:

- **Lighthouse Score**: Target >= 85 (mobile)
- **Code Splitting**: Vendor, Chart.js, and Prism.js in separate chunks
- **Lazy Loading**: Heavy modules loaded on-demand
- **Cache Busting**: Hashed filenames for optimal caching
- **Compression**: Gzip compression for all assets
- **Image Optimization**: WebP format with responsive images
- **Asset Inlining**: Small assets (< 4KB) inlined as base64
- **Polyfill Loading**: Conditional polyfills only for browsers that need them

**Performance Budgets:**
- JavaScript: < 300KB total
- CSS: < 100KB total
- Images: < 500KB per page
- First Contentful Paint: < 1.8s
- Largest Contentful Paint: < 2.5s
- Cumulative Layout Shift: < 0.1

Run `npm run lighthouse:mobile` to verify performance metrics.

## Accessibility

This layout is built with accessibility in mind:

- WCAG 2.1 Level AA compliant
- Full keyboard navigation support
- Screen reader optimized
- Proper ARIA labels and roles
- Skip navigation links
- Focus indicators
- Respects `prefers-reduced-motion`

## License

MIT License

## Support

For questions or issues:
- Email: info@example.com
- Phone: +86 123 456 7890

---

Built with ❤️ by the Development Team
