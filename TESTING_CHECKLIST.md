# Public Layout Testing Checklist

Use this checklist to verify all features work correctly across different devices and browsers.

## ✅ Responsive Design Testing

### Mobile (< 640px)
- [ ] Layout adapts to mobile viewport
- [ ] Mobile menu hamburger icon appears
- [ ] Navigation menu is collapsible
- [ ] All text is readable
- [ ] No horizontal scrolling
- [ ] Touch targets are adequately sized (minimum 44x44px)
- [ ] Hero section scales appropriately
- [ ] Feature cards stack vertically
- [ ] Footer adapts to single column

### Tablet (768px - 1024px)
- [ ] Navigation displays horizontally
- [ ] Content uses appropriate spacing
- [ ] Feature cards display in 2-column grid
- [ ] Images scale properly
- [ ] Footer displays in 2-column grid

### Desktop (> 1024px)
- [ ] Full navigation menu visible
- [ ] Content centered with max-width
- [ ] Feature cards display in 3-column grid
- [ ] Optimal reading line length
- [ ] Footer displays in 4-column grid
- [ ] Scroll controls positioned correctly

## 🌓 Theme System Testing

### Automatic Theme Switching
- [ ] Light theme active between 6 AM - 6 PM
- [ ] Dark theme active between 6 PM - 6 AM
- [ ] Theme transitions smoothly (300ms)
- [ ] All colors update correctly
- [ ] Images/icons remain visible in both themes

### Manual Theme Toggle
- [ ] Toggle button changes theme immediately
- [ ] Theme preference saves to localStorage
- [ ] Theme persists after page reload
- [ ] Theme persists across PJAX navigation
- [ ] Toggle icon animates (rotation)
- [ ] Manual override disables auto-switching

### Theme Colors Verification
- [ ] Light theme: readable text on light background
- [ ] Dark theme: readable text on dark background
- [ ] Primary colors stand out appropriately
- [ ] Links have sufficient contrast
- [ ] Hover states visible in both themes
- [ ] Focus indicators clear in both themes

## ⚡ PJAX Navigation Testing

### Basic Navigation
- [ ] Links with `data-pjax` load via AJAX
- [ ] Content area updates without full reload
- [ ] Header and footer remain unchanged
- [ ] Loading indicator appears during transition
- [ ] Page title updates correctly
- [ ] URL updates in browser address bar

### Browser History
- [ ] Back button returns to previous page
- [ ] Forward button advances to next page
- [ ] History state preserved correctly
- [ ] Page content matches URL

### Error Handling
- [ ] Timeout triggers fallback to full reload
- [ ] 404 errors handled gracefully
- [ ] Network errors trigger fallback
- [ ] Error messages displayed appropriately
- [ ] Loading indicator removed on error

### Active Navigation
- [ ] Current page link highlighted
- [ ] Active state updates after PJAX load
- [ ] Active state cleared from previous link

## 📜 Scroll Controls Testing

### Scroll to Top Button
- [ ] Hidden when page is at top
- [ ] Appears after scrolling down 300px
- [ ] Smoothly scrolls to top when clicked
- [ ] Animates during scroll (fade in/out)
- [ ] Keyboard accessible (Tab navigation)
- [ ] Activates on Enter key
- [ ] Activates on Space key
- [ ] Has appropriate hover state

### Scroll to Bottom Button
- [ ] Hidden when at bottom of page
- [ ] Appears when content extends below fold
- [ ] Smoothly scrolls to bottom when clicked
- [ ] Hides when reaching bottom
- [ ] Keyboard accessible
- [ ] Activates on Enter key
- [ ] Activates on Space key
- [ ] Has appropriate hover state

### Scroll Behavior
- [ ] Smooth scrolling animation (500ms)
- [ ] Custom easing function works
- [ ] Respects `prefers-reduced-motion`
- [ ] Buttons don't overlap content
- [ ] Z-index appropriate for visibility

## 📱 Mobile Menu Testing

### Menu Toggle
- [ ] Hamburger icon visible on mobile
- [ ] Menu opens on tap/click
- [ ] Menu closes on tap outside
- [ ] Menu closes on link click
- [ ] Menu closes on Escape key
- [ ] ARIA attributes update correctly
- [ ] Body scroll disabled when menu open

### Menu Animation
- [ ] Hamburger animates to X icon
- [ ] Menu slides/fades in smoothly
- [ ] Menu slides/fades out smoothly
- [ ] No layout shift during animation

### Menu Content
- [ ] All navigation links visible
- [ ] Theme toggle accessible
- [ ] Action buttons visible
- [ ] Proper spacing and padding
- [ ] Text readable at all times

## ♿ Accessibility Testing

### Keyboard Navigation
- [ ] Skip to main content link works
- [ ] Tab order is logical
- [ ] All interactive elements focusable
- [ ] Focus visible on all elements
- [ ] Enter key activates buttons/links
- [ ] Space key activates buttons
- [ ] Escape key closes mobile menu
- [ ] No keyboard traps

### Screen Reader Testing
- [ ] ARIA labels present and correct
- [ ] ARIA roles appropriate
- [ ] Hidden content not announced
- [ ] Loading states announced
- [ ] Dynamic content updates announced
- [ ] Link purposes clear
- [ ] Button purposes clear
- [ ] Images have alt text (or aria-hidden)

### Focus Management
- [ ] Focus indicators visible (2px outline)
- [ ] Focus color has sufficient contrast
- [ ] Focus order matches visual order
- [ ] Focus returns appropriately after modal/menu close
- [ ] No focus lost to off-screen elements

### Color and Contrast
- [ ] Text contrast ratio ≥ 4.5:1 (normal text)
- [ ] Text contrast ratio ≥ 3:1 (large text)
- [ ] Non-text contrast ratio ≥ 3:1
- [ ] Links distinguishable from text
- [ ] Color not sole indicator of information

### Semantic HTML
- [ ] Proper heading hierarchy (h1 > h2 > h3)
- [ ] Landmarks used (header, nav, main, footer)
- [ ] Lists marked up correctly
- [ ] Buttons vs links used appropriately
- [ ] Forms use labels and fieldsets

## 🌐 Cross-Browser Testing

### Chrome/Edge
- [ ] Layout renders correctly
- [ ] All interactions work
- [ ] PJAX navigation functions
- [ ] Theme switching works
- [ ] Animations smooth
- [ ] Console has no errors

### Firefox
- [ ] Layout renders correctly
- [ ] All interactions work
- [ ] PJAX navigation functions
- [ ] Theme switching works
- [ ] Animations smooth
- [ ] Console has no errors

### Safari (Desktop)
- [ ] Layout renders correctly
- [ ] All interactions work
- [ ] PJAX navigation functions
- [ ] Theme switching works
- [ ] Animations smooth
- [ ] Console has no errors

### Safari (iOS)
- [ ] Layout renders correctly
- [ ] Touch interactions work
- [ ] Mobile menu functions
- [ ] Theme switching works
- [ ] Scroll smooth
- [ ] No console errors

### Chrome (Android)
- [ ] Layout renders correctly
- [ ] Touch interactions work
- [ ] Mobile menu functions
- [ ] Theme switching works
- [ ] Scroll smooth
- [ ] No console errors

## 🚀 Performance Testing

### Load Performance
- [ ] First Contentful Paint < 1.5s
- [ ] Largest Contentful Paint < 2.5s
- [ ] Total Blocking Time < 300ms
- [ ] Cumulative Layout Shift < 0.1
- [ ] CSS file size reasonable (< 50kb)
- [ ] JS file size reasonable (< 50kb)
- [ ] No render-blocking resources

### Runtime Performance
- [ ] Smooth scrolling (60fps)
- [ ] No jank during animations
- [ ] Theme switch immediate
- [ ] PJAX transitions smooth
- [ ] No memory leaks
- [ ] CPU usage reasonable

### Lighthouse Scores
- [ ] Performance: ≥ 90
- [ ] Accessibility: ≥ 95
- [ ] Best Practices: ≥ 90
- [ ] SEO: ≥ 90

## 📄 SEO Testing

### Meta Tags
- [ ] Title tags present and unique
- [ ] Meta descriptions present and unique
- [ ] Character encoding specified
- [ ] Viewport meta tag present
- [ ] Language attribute set

### Content Structure
- [ ] Single H1 per page
- [ ] Logical heading hierarchy
- [ ] Semantic HTML elements used
- [ ] Content meaningful and accessible
- [ ] Links have descriptive text

### Technical SEO
- [ ] Clean URL structure
- [ ] No broken links
- [ ] Fast page load times
- [ ] Mobile-friendly
- [ ] HTTPS (if applicable)

## 🔍 Visual Regression Testing

### Layout Consistency
- [ ] No unexpected layout shifts
- [ ] Consistent spacing throughout
- [ ] Aligned elements properly
- [ ] No overflowing content
- [ ] No clipped text

### Typography
- [ ] Font loads correctly
- [ ] Font sizes appropriate
- [ ] Line heights comfortable
- [ ] Letter spacing optimal
- [ ] Text colors readable

### Components
- [ ] Buttons styled consistently
- [ ] Cards align in grid
- [ ] Footer elements aligned
- [ ] Navigation items evenly spaced
- [ ] Icons sized appropriately

## 🔧 Edge Cases Testing

### Content Variations
- [ ] Very long page titles
- [ ] Long navigation menu items
- [ ] Missing images handled
- [ ] Empty content sections
- [ ] Very long content pages
- [ ] Very short content pages

### Browser Conditions
- [ ] JavaScript disabled (graceful degradation)
- [ ] Cookies disabled
- [ ] localStorage unavailable
- [ ] Slow network connection
- [ ] Offline mode
- [ ] Print styles (if applicable)

### User Preferences
- [ ] Respects reduced motion preference
- [ ] Handles high contrast mode
- [ ] Works with browser zoom (up to 200%)
- [ ] Works with text-only zoom
- [ ] Works with dark mode OS preference

## 📝 Documentation Testing

### Code Quality
- [ ] HTML validates (W3C validator)
- [ ] CSS validates
- [ ] JavaScript has no console errors
- [ ] Indentation consistent
- [ ] Comments where needed

### Documentation Accuracy
- [ ] README instructions work
- [ ] Implementation guide accurate
- [ ] Code examples functional
- [ ] File paths correct
- [ ] Dependencies listed

---

## Testing Notes

**Test Date:** _________________

**Tested By:** _________________

**Browser/Device:** _________________

**Issues Found:**
1. ___________________________________
2. ___________________________________
3. ___________________________________

**Additional Notes:**
_____________________________________
_____________________________________
_____________________________________

---

**Testing Status:**
- [ ] All critical tests passed
- [ ] All recommended tests passed
- [ ] Known issues documented
- [ ] Ready for production
