# Logbook Browser - Responsive Layout Testing Checklist

**Feature:** Logbook Browser with Filtering
**Created:** 2026-02-07
**Purpose:** Manual testing checklist for responsive design validation

## Test Environment Setup

### Prerequisites
- Active event with at least 50 logged contacts
- Test data includes:
  - Contacts with various bands (20m, 40m, 80m, etc.)
  - Contacts with various modes (SSB, CW, FT8, etc.)
  - Mix of duplicate and non-duplicate contacts
  - Contacts from multiple stations and operators

### Browser Testing Matrix
Test in the following browsers (minimum):
- [ ] Chrome/Edge (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest, macOS/iOS)
- [ ] Mobile Safari (iOS)
- [ ] Chrome Mobile (Android)

## Viewport Breakpoints

Test at these specific widths:

| Width | Breakpoint | Device Example | Primary Focus |
|-------|------------|----------------|---------------|
| 375px | Base (mobile) | iPhone SE | Button overflow, text truncation, collapsible filters |
| 640px | sm | Large phone | Layout transitions, filter panel behavior |
| 768px | md | iPad | Form layouts, stats grid, table view activation |
| 1024px | lg | Desktop | Two-column layout, filter panel always visible |
| 1280px | xl | Large desktop | Wide content areas, full table display |

## Test Cases

### 1. Filter Panel Behavior

#### Mobile (< 1024px)
- [ ] **375px**: Filter panel is collapsed by default
- [ ] **375px**: Tapping "Filters" button expands the panel
- [ ] **375px**: Filters stack vertically
- [ ] **375px**: Filter inputs are full width (`w-full`)
- [ ] **640px**: Filter panel still collapsible
- [ ] **768px**: Filter panel still collapsible

#### Desktop (≥ 1024px)
- [ ] **1024px**: Filter panel is always visible (not collapsible)
- [ ] **1024px**: Filter panel appears in left column
- [ ] **1280px**: Filter panel maintains good proportions

#### Filter Controls
- [ ] Band dropdown renders correctly at all breakpoints
- [ ] Mode dropdown renders correctly at all breakpoints
- [ ] Station dropdown renders correctly at all breakpoints
- [ ] Operator dropdown renders correctly at all breakpoints
- [ ] Callsign search input is accessible
- [ ] Time range picker works on mobile (touch-friendly)
- [ ] Section dropdown renders correctly
- [ ] Duplicate filter toggle works
- [ ] "Reset Filters" button is accessible at all breakpoints

### 2. Results View (Table vs. Cards)

#### Mobile Card View (< 768px)
- [ ] **375px**: Results display as stacked cards
- [ ] **375px**: Each card shows: Callsign, Band/Mode, Time, Points
- [ ] **375px**: Duplicate badge is visible on duplicate contacts
- [ ] **375px**: GOTA badge is visible on GOTA contacts
- [ ] **375px**: No horizontal scrolling
- [ ] **640px**: Cards maintain good spacing and layout

#### Tablet/Desktop Table View (≥ 768px)
- [ ] **768px**: Results display as table
- [ ] **768px**: Table columns: QSO Time, Callsign, Band, Mode, Section, Points, Station, Logger
- [ ] **768px**: Table headers are visible
- [ ] **768px**: Table rows are touch-friendly (min 44px height)
- [ ] **1024px**: Full table view with all columns
- [ ] **1024px**: Duplicate contacts have distinct visual styling
- [ ] **1280px**: Table doesn't overflow or cause horizontal scroll

### 3. Stats Summary Grid

#### Responsive Column Count
- [ ] **375px**: 2 columns (`grid-cols-2`)
- [ ] **640px**: 2 columns (still `grid-cols-2`)
- [ ] **768px**: 3 columns (`md:grid-cols-3`)
- [ ] **1024px**: 6 columns (`lg:grid-cols-6`)
- [ ] **1280px**: 6 columns maintained

#### Stat Cards
- [ ] All stats visible: Total QSOs, Total Points, Unique Sections, QSOs by Band, QSOs by Mode
- [ ] Stat values are readable at all breakpoints
- [ ] Cards have appropriate spacing (`gap-4`)

### 4. Button Sizing (Touch-Friendly)

#### Mobile (< 640px)
- [ ] **375px**: All buttons have `min-h-[2.75rem]` (44px minimum)
- [ ] **375px**: Export button is touch-friendly
- [ ] **375px**: Filter buttons (Apply, Reset) are touch-friendly
- [ ] **375px**: Pagination buttons are touch-friendly

#### Desktop (≥ 640px)
- [ ] **640px**: Buttons use `sm:min-h-[1.75rem]` (28px compact)
- [ ] **1024px**: Buttons maintain compact sizing

### 5. No Horizontal Scrolling

Test at each breakpoint:
- [ ] **375px**: No horizontal scroll on any section
- [ ] **640px**: No horizontal scroll on any section
- [ ] **768px**: No horizontal scroll on any section
- [ ] **1024px**: No horizontal scroll on any section
- [ ] **1280px**: No horizontal scroll on any section

**Critical Check:** Long callsigns or data must truncate properly with `truncate` class

### 6. Pagination Controls

- [ ] **375px**: Pagination buttons stack vertically or wrap
- [ ] **375px**: "Next"/"Previous" buttons are touch-friendly
- [ ] **768px**: Pagination inline, centered
- [ ] **1024px**: Pagination maintains good spacing

### 7. Export Functionality

- [ ] **375px**: Export button is visible and accessible
- [ ] **375px**: Export button doesn't overflow or wrap awkwardly
- [ ] **1024px**: Export button appears in appropriate location

### 8. Loading States

- [ ] **All breakpoints**: Loading spinner/skeleton is visible during filtering
- [ ] **All breakpoints**: Loading states don't cause layout shift
- [ ] **All breakpoints**: Stats show loading skeleton

### 9. Empty States

- [ ] **All breakpoints**: "No contacts found" message displays correctly
- [ ] **All breakpoints**: "No active event" message is centered and readable

### 10. Text Truncation

#### Mobile (375px - 640px)
- [ ] Long callsigns truncate with ellipsis
- [ ] Station names truncate in table/cards
- [ ] Section names don't overflow

#### Desktop (≥ 1024px)
- [ ] Text has more room, less truncation
- [ ] Full table columns don't cause overflow

## Critical Responsive Patterns Verification

### Pattern 1: Parent-Child Breakpoint Matching
- [ ] Stats grid uses `grid-cols-2 md:grid-cols-3 lg:grid-cols-6`
- [ ] If results use two-column grid, child elements use matching breakpoints

### Pattern 2: Flex Direction Changes
- [ ] Filter panel content stacks on mobile (`flex-col`)
- [ ] Filter panel content goes horizontal on desktop (`lg:flex-row`)

### Pattern 3: Spacing Consistency
- [ ] Gaps are responsive: `gap-4 sm:gap-6`
- [ ] Padding is responsive: `p-2 sm:p-3` or `p-4 sm:p-6`

## Acceptance Criteria

All tests must pass:
- ✅ Filter panel is collapsible on mobile (< 1024px)
- ✅ Results show as cards on mobile, table on tablet/desktop
- ✅ Stats grid: 2 cols (mobile) → 3 cols (md) → 6 cols (lg)
- ✅ Buttons are touch-friendly on mobile (44px min)
- ✅ No horizontal scrolling at any viewport width
- ✅ Text truncates properly, no overflow
- ✅ All interactive elements accessible at all breakpoints

## Automated Testing Limitations

**Note:** These tests are designed for manual execution because:
1. Laravel Dusk is not currently installed in the project
2. MaryUI components don't render properly in isolated test environments
3. Visual layout testing is more reliable with real browser rendering
4. Responsive behavior is best verified with actual device/browser testing

## Future Automation

When Laravel Dusk is added to the project, the following can be automated:
- Viewport resizing and element visibility checks
- Filter panel collapse/expand behavior
- Table/card view switching
- Button size measurements
- Horizontal scroll detection
- Screenshot comparison at each breakpoint

## Test Results Log

| Date | Tester | Pass/Fail | Notes |
|------|--------|-----------|-------|
| | | | |
| | | | |
| | | | |

## References

- Responsive Patterns Documentation: `docs/responsive-patterns.md`
- Tailwind Breakpoints: https://tailwindcss.com/docs/responsive-design
- Touch Target Sizing: https://www.w3.org/WAI/WCAG21/Understanding/target-size.html (min 44x44px)
