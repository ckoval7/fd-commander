# Responsive Layout Patterns

This document defines the responsive layout conventions used in this project. Following these patterns prevents layout bugs where elements overlap or break at certain viewport sizes.

## Breakpoint Reference

| Breakpoint | Min Width | Typical Use |
|------------|-----------|-------------|
| (base) | 0px | Mobile phones (portrait) |
| `sm:` | 640px | Large phones, small tablets |
| `md:` | 768px | Tablets |
| `lg:` | 1024px | Laptops, small desktops |
| `xl:` | 1280px | Large desktops |

## Core Principle: Match Child Breakpoints to Parent Containers

**This is the #1 source of layout bugs.** When a parent container changes layout at a breakpoint, children must use the same or later breakpoint.

```blade
{{-- Parent uses lg: for two columns --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- WRONG: Child uses sm: - will be horizontal when parent is still single column --}}
    <div class="flex flex-col sm:flex-row">
        <div class="flex-1">Content</div>
        <x-button label="Long Button Text" class="sm:w-auto" />
    </div>

    {{-- CORRECT: Child uses lg: to match parent --}}
    <div class="flex flex-col lg:flex-row">
        <div class="flex-1">Content</div>
        <x-button label="Long Button Text" class="lg:w-auto" />
    </div>
</div>
```

## Standard Grid Patterns

### Page-Level Two-Column Layout
Used for split views like equipment assignment, settings panels.

```blade
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
    <x-card>Left panel</x-card>
    <x-card>Right panel</x-card>
</div>
```

**Child elements should use `lg:` breakpoints** since the grid is single-column until `lg`.

### Card Grid (3 columns max)
Used for station lists, dashboard cards.

```blade
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
    @foreach($items as $item)
        <x-card>{{ $item->name }}</x-card>
    @endforeach
</div>
```

### Form Field Grid
Used for forms with side-by-side fields.

```blade
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <x-input label="First Name" />
    <x-input label="Last Name" />
</div>
```

### Stat Cards Grid
Progressive column reduction for stat displays.

```blade
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
    @foreach($stats as $stat)
        <div class="stat">...</div>
    @endforeach
</div>
```

## Flex Patterns

### Page Header with Actions
Title on left, buttons on right. Stacks on mobile.

```blade
<div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
    <div>
        <h1 class="text-2xl font-bold">Page Title</h1>
        <p class="text-base-content/70">Subtitle</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <x-button label="Action 1" />
        <x-button label="Action 2" />
    </div>
</div>
```

### Card Content with Action Button
Content on left, button on right. **Match breakpoint to parent container.**

```blade
{{-- Inside a lg:grid-cols-2 parent --}}
<div class="flex flex-col lg:flex-row lg:items-start gap-2 lg:gap-3">
    <div class="flex-1 min-w-0">
        <div class="font-medium truncate">Item Name</div>
        <div class="text-sm text-base-content/70">Description</div>
    </div>
    <x-button
        label="Long Action Text"
        class="w-full lg:w-auto flex-shrink-0"
    />
</div>

{{-- Inside a sm:grid-cols-2 parent (or no grid parent) --}}
<div class="flex flex-col sm:flex-row sm:items-start gap-2 sm:gap-3">
    <div class="flex-1 min-w-0">
        <div class="font-medium truncate">Item Name</div>
    </div>
    <x-button
        label="Short"
        class="w-full sm:w-auto flex-shrink-0"
    />
</div>
```

### Card Footer Actions
Buttons stack on mobile, inline on larger screens.

```blade
<div class="flex flex-col sm:flex-row gap-2 pt-3 border-t border-base-300">
    <x-button label="Edit" class="btn-outline flex-1 sm:flex-none" />
    <x-button label="Delete" class="btn-ghost text-error" />
</div>
```

## Button Sizing

### Touch-Friendly Mobile Buttons
Use larger touch targets on mobile, compact on desktop.

```blade
<x-button
    label="Action"
    class="btn-sm min-h-[2.75rem] sm:min-h-[1.75rem]"
/>
```

- Mobile: 44px (2.75rem) minimum height for touch
- Desktop: 28px (1.75rem) compact height

### Full-Width to Auto-Width

```blade
<x-button
    label="Submit"
    class="w-full sm:w-auto"
/>
```

### Button Label Length Guidelines

| Context | Max Characters | Example |
|---------|---------------|---------|
| Inside narrow card (lg:grid-cols-2 child) | 8-10 | "Assign", "Edit" |
| Inside medium card (sm:grid-cols-2 child) | 12-15 | "Save Changes" |
| Page header actions | 15-20 | "Create New Station" |
| Full-width mobile button | No limit | "Commit & Assign Equipment" |

**If button text exceeds limits**, either:
1. Use a later breakpoint for horizontal layout
2. Shorten the label
3. Use icon-only on smaller screens with tooltip

## Badge Sizing

Scale badges with viewport.

```blade
<x-badge value="Status" class="badge-xs sm:badge-sm" />
<x-badge value="Important" class="badge-sm sm:badge-md" />
```

## Text Sizing

```blade
{{-- Labels and small text --}}
<span class="text-xs sm:text-sm">Label</span>

{{-- Body text --}}
<p class="text-sm sm:text-base">Content</p>

{{-- Headings --}}
<h2 class="text-lg sm:text-xl">Section Title</h2>
```

## Spacing

### Gaps
- `gap-2`: Between tightly related items (buttons in a group)
- `gap-3`: Between list items within a section
- `gap-4`: Between sections, default card padding
- `gap-6`: Between major page sections

### Responsive Gaps

```blade
<div class="gap-4 sm:gap-6">...</div>
```

### Padding

```blade
{{-- Card content --}}
<div class="p-2 sm:p-3">...</div>

{{-- Page sections --}}
<div class="p-4 sm:p-6">...</div>
```

## Common Patterns

### Truncating Long Text
Always use `min-w-0` on flex children and `truncate` on text.

```blade
<div class="flex items-center gap-3">
    <div class="flex-1 min-w-0">
        <div class="font-medium truncate">Very Long Equipment Name Here</div>
    </div>
    <x-button label="Edit" class="flex-shrink-0" />
</div>
```

### Scrollable Tables on Mobile

```blade
<div class="overflow-x-auto">
    <table class="table">...</table>
</div>
```

### Hide/Show at Breakpoints

```blade
{{-- Mobile only --}}
<div class="lg:hidden">Mobile nav</div>

{{-- Desktop only --}}
<div class="hidden lg:block">Desktop header</div>

{{-- Hide text, show icon on mobile --}}
<x-button icon="o-plus" class="sm:hidden" />
<x-button label="Add Item" icon="o-plus" class="hidden sm:inline-flex" />
```

## QA Checklist

Test layouts at these widths:

| Width | Device | Watch For |
|-------|--------|-----------|
| 375px | iPhone SE | Button overflow, text truncation |
| 640px | sm breakpoint | Layout transitions |
| 768px | md breakpoint, iPad | Form layouts |
| 1024px | lg breakpoint | Two-column layouts |
| 1280px | xl breakpoint | Wide content areas |

**Especially test the "awkward middle"**: 640px-800px where grids may be 2 columns but cells are narrow.

## Quick Reference: Parent → Child Breakpoint Matching

| Parent Grid | Child Flex Breakpoint |
|-------------|----------------------|
| `lg:grid-cols-2` | `lg:flex-row` |
| `md:grid-cols-2` | `md:flex-row` |
| `sm:grid-cols-2` | `sm:flex-row` |
| `lg:grid-cols-3` | `lg:flex-row` (or `md:` if content is short) |
| No grid (full width) | `sm:flex-row` usually fine |

## Real Examples from Codebase

### Equipment Assignment (Two-Column)
`resources/views/livewire/stations/equipment-assignment.blade.php`

```blade
{{-- Parent: lg:grid-cols-2 --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">

    {{-- Catalog items use lg: to match --}}
    <div class="flex flex-col lg:flex-row lg:items-start gap-2 lg:gap-3">
        <div class="flex-1 min-w-0">...</div>
        <x-button label="Commit & Assign" class="w-full lg:w-auto" />
    </div>
</div>
```

### Station List (Three-Column Cards)
`resources/views/livewire/stations/stations-list.blade.php`

```blade
{{-- Parent: sm → lg progression --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">

    {{-- Card footer actions use sm: since cards are side-by-side at sm: --}}
    <div class="flex flex-col sm:flex-row gap-2">
        <x-button label="Edit" class="flex-1 sm:flex-none" />
    </div>
</div>
```
