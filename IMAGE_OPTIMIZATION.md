# Image Optimization Guide

## Overview

This guide provides best practices for optimizing images in the application to ensure fast loading times and good user experience.

## Image Formats

### Recommended Formats

1. **WebP** - Primary format (superior compression)
   - 25-35% smaller than JPEG/PNG
   - Supports transparency and animation
   - Excellent browser support (95%+)

2. **JPEG** - Fallback for photos
   - Best for photographs
   - Good compression
   - No transparency

3. **PNG** - Fallback for graphics
   - Best for graphics with transparency
   - Lossless compression
   - Larger file sizes

4. **SVG** - For icons and logos
   - Vector format (scalable)
   - Very small file size
   - Perfect for icons

### Format Selection Guide

```
Icon/Logo → SVG
Photo → WebP (with JPEG fallback)
Graphic with transparency → WebP (with PNG fallback)
Complex graphic → WebP (with PNG fallback)
```

## Responsive Images

### Picture Element with WebP Fallback

```html
<picture>
  <!-- WebP format for modern browsers -->
  <source 
    srcset="
      {{ asset('build/images/hero-320.webp') }} 320w,
      {{ asset('build/images/hero-640.webp') }} 640w,
      {{ asset('build/images/hero-1024.webp') }} 1024w,
      {{ asset('build/images/hero-1920.webp') }} 1920w
    "
    sizes="(max-width: 640px) 320px,
           (max-width: 1024px) 640px,
           (max-width: 1920px) 1024px,
           1920px"
    type="image/webp"
  >
  
  <!-- JPEG fallback -->
  <source 
    srcset="
      {{ asset('build/images/hero-320.jpg') }} 320w,
      {{ asset('build/images/hero-640.jpg') }} 640w,
      {{ asset('build/images/hero-1024.jpg') }} 1024w,
      {{ asset('build/images/hero-1920.jpg') }} 1920w
    "
    sizes="(max-width: 640px) 320px,
           (max-width: 1024px) 640px,
           (max-width: 1920px) 1024px,
           1920px"
    type="image/jpeg"
  >
  
  <!-- Final fallback -->
  <img 
    src="{{ asset('build/images/hero-1024.jpg') }}" 
    alt="Hero image"
    loading="lazy"
    decoding="async"
  >
</picture>
```

### Simple Responsive Image

```html
<img 
  srcset="
    {{ asset('build/images/product-small.webp') }} 320w,
    {{ asset('build/images/product-medium.webp') }} 640w,
    {{ asset('build/images/product-large.webp') }} 1024w
  "
  sizes="(max-width: 640px) 320px,
         (max-width: 1024px) 640px,
         1024px"
  src="{{ asset('build/images/product-medium.webp') }}"
  alt="Product image"
  loading="lazy"
  decoding="async"
>
```

## Lazy Loading

### Native Lazy Loading

```html
<!-- Browser-native lazy loading -->
<img 
  src="{{ asset('build/images/photo.webp') }}" 
  alt="Description"
  loading="lazy"
  decoding="async"
>
```

### Intersection Observer (for better control)

```html
<!-- Use data-src for lazy loading -->
<img 
  data-src="{{ asset('build/images/photo.webp') }}" 
  alt="Description"
  class="lazy-image"
>
```

JavaScript is automatically included in `media-preview.js` to handle lazy loading with IntersectionObserver.

## Image Sizing Guidelines

### Standard Breakpoints

```
Mobile:    320px, 480px, 640px
Tablet:    768px, 1024px
Desktop:   1280px, 1440px, 1920px
Retina:    2x versions for critical images
```

### Recommended Sizes

| Image Type | Dimensions | Format | Quality |
|------------|-----------|--------|---------|
| Hero image | 1920x1080 | WebP + JPEG | 80% |
| Thumbnail | 320x180 | WebP + JPEG | 80% |
| Card image | 640x360 | WebP + JPEG | 80% |
| Icon | 24x24, 48x48 | SVG or PNG | - |
| Logo | Variable | SVG preferred | - |
| Avatar | 128x128 | WebP + JPEG | 85% |

## Optimization Tools

### Command Line Tools

#### ImageMagick (Resize & Convert)

```bash
# Resize image
convert input.jpg -resize 1920x1080 output.jpg

# Convert to WebP
convert input.jpg -quality 80 output.webp

# Create multiple sizes
for size in 320 640 1024 1920; do
  convert input.jpg -resize ${size}x output-${size}.jpg
  convert input.jpg -resize ${size}x -quality 80 output-${size}.webp
done
```

#### cwebp (WebP Encoder)

```bash
# Convert JPEG to WebP
cwebp -q 80 input.jpg -o output.webp

# Convert PNG to WebP (with transparency)
cwebp -q 80 -lossless input.png -o output.webp

# Batch conversion
for file in *.jpg; do
  cwebp -q 80 "$file" -o "${file%.jpg}.webp"
done
```

#### OptiPNG (PNG Optimization)

```bash
# Optimize PNG
optipng -o7 image.png

# Batch optimize
optipng -o7 *.png
```

#### JPEGoptim (JPEG Optimization)

```bash
# Optimize JPEG
jpegoptim --max=85 image.jpg

# Batch optimize
jpegoptim --max=85 *.jpg
```

### Online Tools

- [Squoosh](https://squoosh.app/) - Google's image optimizer
- [TinyPNG](https://tinypng.com/) - PNG/JPEG compression
- [SVGOMG](https://jakearchibald.github.io/svgomg/) - SVG optimization

## Build Process Integration

Images placed in `resources/images/` are automatically optimized during build:

```javascript
// vite.config.js includes vite-plugin-imagemin
// Automatically optimizes images during build
```

### Manual Optimization Script

Create `scripts/optimize-images.sh`:

```bash
#!/bin/bash

# Optimize all images in resources/images/
cd resources/images

# Optimize JPEGs
find . -name "*.jpg" -o -name "*.jpeg" | while read file; do
  jpegoptim --max=85 "$file"
  cwebp -q 80 "$file" -o "${file%.*}.webp"
done

# Optimize PNGs
find . -name "*.png" | while read file; do
  optipng -o7 "$file"
  cwebp -q 80 "$file" -o "${file%.*}.webp"
done

# Optimize SVGs
find . -name "*.svg" | while read file; do
  svgo "$file"
done

echo "Image optimization complete!"
```

## Performance Best Practices

### 1. Use Appropriate Dimensions

Don't serve 4000x3000 images when you only need 400x300:

```html
<!-- Bad: Serving full-size image -->
<img src="full-size-4000x3000.jpg" style="width: 400px;">

<!-- Good: Serving appropriately sized image -->
<img src="optimized-400x300.webp" width="400" height="300">
```

### 2. Specify Width and Height

Prevent layout shift by specifying dimensions:

```html
<!-- Good: Prevents CLS -->
<img 
  src="image.webp" 
  width="800" 
  height="600" 
  alt="Description"
>
```

### 3. Use Loading Priority

```html
<!-- Critical above-the-fold images -->
<img src="hero.webp" loading="eager" fetchpriority="high">

<!-- Below-the-fold images -->
<img src="content.webp" loading="lazy" fetchpriority="low">
```

### 4. Implement Blur-up Technique

```html
<div class="image-wrapper">
  <!-- Tiny blurred placeholder (inline base64) -->
  <img 
    src="data:image/jpeg;base64,/9j/4AAQSkZJRg..." 
    class="placeholder"
    aria-hidden="true"
  >
  
  <!-- Actual image (lazy loaded) -->
  <img 
    data-src="full-image.webp" 
    class="lazy-image"
    alt="Description"
  >
</div>
```

```css
.image-wrapper {
  position: relative;
  overflow: hidden;
}

.placeholder {
  position: absolute;
  filter: blur(10px);
  transform: scale(1.1);
}

.lazy-image {
  opacity: 0;
  transition: opacity 0.3s;
}

.lazy-image.loaded {
  opacity: 1;
}
```

### 5. Avoid Image Sprites for HTTP/2

With HTTP/2 multiplexing, individual optimized images often perform better than sprites.

### 6. Use CSS for Simple Graphics

```css
/* Instead of loading a gradient image */
.gradient {
  background: linear-gradient(to bottom, #667eea 0%, #764ba2 100%);
}

/* Instead of loading a simple shape */
.circle {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background: #667eea;
}
```

## Image CDN Integration (Optional)

For high-traffic sites, consider using an image CDN:

```php
// Helper function for image URLs
function imageUrl($path, $width = null, $height = null, $quality = 80) {
    $base = config('images.cdn_url', asset('build/images'));
    $params = http_build_query(array_filter([
        'w' => $width,
        'h' => $height,
        'q' => $quality,
        'fm' => 'webp',
    ]));
    
    return $params ? "{$base}/{$path}?{$params}" : "{$base}/{$path}";
}
```

```blade
{{-- Usage in Blade --}}
<img src="{{ imageUrl('hero.jpg', 1920, 1080) }}" alt="Hero">
```

## Monitoring & Analytics

### Core Web Vitals Impact

Images affect:
- **LCP (Largest Contentful Paint)** - Hero images
- **CLS (Cumulative Layout Shift)** - Unsized images
- **FID (First Input Delay)** - Heavy image processing

### Lighthouse Audits

```bash
# Check image optimization
npm run lighthouse

# Look for these warnings:
# - "Serve images in next-gen formats"
# - "Properly size images"
# - "Defer offscreen images"
# - "Efficiently encode images"
```

### Image Size Budget

| Page Type | Total Image Size |
|-----------|-----------------|
| Homepage | < 500 KB |
| List page | < 300 KB |
| Detail page | < 400 KB |
| Article | < 600 KB |

## Troubleshooting

### Images not displaying

1. Check file path is correct
2. Verify file exists in `public/build/images/`
3. Check file permissions
4. Inspect network tab for 404 errors

### WebP not loading

1. Check browser support (should have JPEG fallback)
2. Verify WebP files were generated
3. Check MIME type configuration on server

### Slow image loading

1. Check image file size (should be < 200 KB)
2. Verify lazy loading is working
3. Check for multiple large images loading simultaneously
4. Use network throttling to test

## Resources

- [Web.dev Image Optimization](https://web.dev/fast/#optimize-your-images)
- [MDN Responsive Images](https://developer.mozilla.org/en-US/docs/Learn/HTML/Multimedia_and_embedding/Responsive_images)
- [WebP Documentation](https://developers.google.com/speed/webp)
- [Can I Use - WebP](https://caniuse.com/webp)
