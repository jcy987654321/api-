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
- 🚀 **Performance**: Optimized CSS and minimal JavaScript

## Quick Start

### Prerequisites

- Node.js and npm (for SCSS compilation)
- Modern browser with JavaScript enabled

### Installation

1. Clone the repository:
   ```bash
   git clone <repository-url>
   cd <project-directory>
   ```

2. Install Sass (if not already installed):
   ```bash
   npm install -g sass
   ```

3. Compile SCSS to CSS:
   ```bash
   npm run sass
   ```

4. Open `demo.html` in your browser to see the layout in action

### For Laravel Integration

See the [Implementation Guide](IMPLEMENTATION_GUIDE.md) for detailed Laravel integration steps.

## Documentation

- **[Implementation Guide](IMPLEMENTATION_GUIDE.md)**: Step-by-step setup and integration
- **[Public Layout Documentation](PUBLIC_LAYOUT_DOCS.md)**: Complete feature documentation and API reference

## Project Structure

```
project/
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   └── public.blade.php          # Master Blade layout
│   │   └── public/
│   │       ├── home.blade.php            # Homepage
│   │       ├── features.blade.php        # Features page
│   │       └── about.blade.php           # About page
│   └── scss/
│       └── public/
│           ├── _variables.scss           # Design tokens
│           ├── _base.scss                # Base styles
│           ├── _components.scss          # Component styles
│           └── styles.scss               # Main entry point
├── public/
│   ├── css/
│   │   └── public.css                    # Compiled CSS
│   └── js/
│       └── public.js                     # Main JavaScript
├── demo.html                             # Standalone demo
├── package.json                          # NPM scripts
└── README.md                             # This file
```

## Theme System

The layout features an intelligent theme system:

- **Auto-switching**: Automatically switches between light (6 AM - 6 PM) and dark themes
- **Manual override**: Users can manually toggle theme, with preference saved to localStorage
- **Smooth transitions**: CSS variable-based theming with graceful animations
- **Persistent**: Theme preference persists across PJAX navigation

## Browser Support

- Chrome/Edge (latest 2 versions)
- Firefox (latest 2 versions)
- Safari (latest 2 versions)
- iOS Safari 12+
- Chrome Android 90+

## Development

### Available Scripts

```bash
# Compile SCSS once
npm run sass

# Watch and recompile on changes
npm run sass:watch

# Build for production (minified)
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
