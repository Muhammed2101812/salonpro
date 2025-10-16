# Design System - SalonPro

## Overview

This document outlines the design system, UI components, and visual guidelines for SalonPro.

---

## Design Principles

1. **Simplicity** - Clean, intuitive interfaces
2. **Consistency** - Uniform patterns across the app
3. **Accessibility** - WCAG 2.1 AA compliance
4. **Responsiveness** - Mobile-first approach
5. **Performance** - Fast loading, smooth interactions

---

## Color Palette

### Primary Colors
```css
--primary-50: #f0f9ff;
--primary-100: #e0f2fe;
--primary-500: #0ea5e9; /* Main brand color */
--primary-600: #0284c7;
--primary-700: #0369a1;
```

### Secondary Colors
```css
--secondary-50: #f8fafc;
--secondary-500: #64748b;
--secondary-700: #334155;
```

### Semantic Colors
```css
--success: #10b981;
--warning: #f59e0b;
--error: #ef4444;
--info: #3b82f6;
```

### Neutral Colors
```css
--gray-50: #f9fafb;
--gray-100: #f3f4f6;
--gray-500: #6b7280;
--gray-900: #111827;
```

---

## Typography

### Font Family
```css
font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
```

### Font Sizes
```css
--text-xs: 0.75rem;    /* 12px */
--text-sm: 0.875rem;   /* 14px */
--text-base: 1rem;     /* 16px */
--text-lg: 1.125rem;   /* 18px */
--text-xl: 1.25rem;    /* 20px */
--text-2xl: 1.5rem;    /* 24px */
--text-3xl: 1.875rem;  /* 30px */
--text-4xl: 2.25rem;   /* 36px */
```

### Font Weights
```css
--font-normal: 400;
--font-medium: 500;
--font-semibold: 600;
--font-bold: 700;
```

---

## Spacing System

Based on 4px grid:
```css
--space-1: 0.25rem;  /* 4px */
--space-2: 0.5rem;   /* 8px */
--space-3: 0.75rem;  /* 12px */
--space-4: 1rem;     /* 16px */
--space-6: 1.5rem;   /* 24px */
--space-8: 2rem;     /* 32px */
--space-12: 3rem;    /* 48px */
--space-16: 4rem;    /* 64px */
```

---

## Layout

### Container Widths
- Mobile: 100%
- Tablet: 768px
- Desktop: 1024px
- Wide: 1280px
- Max: 1536px

### Breakpoints
```css
sm: 640px
md: 768px
lg: 1024px
xl: 1280px
2xl: 1536px
```

---

## Components

### Buttons

**Primary Button**
```vue
<button class="btn btn-primary">
  Save
</button>
```

**Variants:**
- Primary (main actions)
- Secondary (alternative actions)
- Danger (destructive actions)
- Ghost (subtle actions)
- Link (text-only)

**Sizes:**
- sm (small)
- md (default)
- lg (large)

---

### Forms

**Input Field**
```vue
<input
  type="text"
  class="input"
  placeholder="Enter name"
/>
```

**Components:**
- Text input
- Number input
- Email input
- Password input
- Textarea
- Select dropdown
- Checkbox
- Radio button
- Date picker
- Time picker
- File upload

---

### Cards
```vue
<div class="card">
  <div class="card-header">
    <h3>Title</h3>
  </div>
  <div class="card-body">
    Content
  </div>
</div>
```

---

### Modals
- Confirmation dialogs
- Form modals
- Detail views
- Image galleries

---

### Tables
- Sortable columns
- Filterable data
- Pagination
- Row selection
- Actions column

---

### Navigation
- Top navbar
- Sidebar menu
- Breadcrumbs
- Tabs
- Pagination

---

### Feedback

**Toast Notifications**
- Success messages
- Error messages
- Warning messages
- Info messages

**Loading States**
- Spinner
- Skeleton loaders
- Progress bars

---

## Icons

Using **Heroicons**
- Outline style for navigation
- Solid style for buttons
- 20px and 24px sizes

---

## Responsive Design

### Mobile First
- Design for mobile
- Progressive enhancement
- Touch-friendly (44px minimum)

### Breakpoint Strategy
- Mobile: 1 column
- Tablet: 2 columns
- Desktop: 3-4 columns

---

## Accessibility

### Requirements
- WCAG 2.1 AA compliance
- Keyboard navigation
- Screen reader support
- Focus indicators
- Color contrast ratios
- Alt text for images
- ARIA labels

### Focus States
- Clear focus indicators
- Skip to content link
- Keyboard shortcuts

---

## Dark Mode

**Support:** Optional (not phase 1)

---

## Animation

### Transitions
```css
--transition-fast: 150ms;
--transition-base: 200ms;
--transition-slow: 300ms;
```

### Easing
```css
--ease-in-out: cubic-bezier(0.4, 0, 0.2, 1);
--ease-out: cubic-bezier(0, 0, 0.2, 1);
```

---

## Multi-Language Support

### Text Direction
- LTR (left-to-right) for English
- LTR for Turkish
- RTL support for future (Arabic, Hebrew)

### Font Considerations
- Inter supports Turkish characters
- Proper line height for readability

---

**Document Version:** 1.0
**Last Updated:** 2025-01-15
