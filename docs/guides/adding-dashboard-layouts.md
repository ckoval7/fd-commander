# Adding Dashboard Layouts

This guide walks you through creating a new dashboard layout from start to finish.

## Quick Start

A layout is a Livewire component that determines how widgets are arranged and displayed. Each layout:
- Controls widget grid arrangement
- Can support custom styling themes
- May restrict customization (TV mode is non-customizable)
- Handles widget filtering by permission and visibility
- Supports real-time updates and F-key toggles

## What's a Layout?

Layouts are different views of the same dashboard data:
- **Default Layout**: Customizable widget grid for normal users
- **TV Layout**: Large-text, dark-theme display for remote viewing
- **Future Options**: Mobile layout, kiosk mode, editor mode, etc.

Users select layouts from a dropdown and the choice persists in localStorage.

## Step 1: Create the Livewire Component

Generate a new Livewire component in the `Dashboard\Layouts` namespace:

```bash
php artisan make:livewire Dashboard/Layouts/MyLayout --no-interaction
```

This creates:
- `app/Livewire/Dashboard/Layouts/MyLayout.php` (component class)
- `resources/views/livewire/dashboard/layouts/my-layout.blade.php` (view)

## Step 2: Build the Component Class

Here's a template for a layout component:

```php
<?php

namespace App\Livewire\Dashboard\Layouts;

use App\Models\Event;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class MyLayout extends Component
{
    public ?Event $event = null;

    public function mount(?Event $event = null): void
    {
        $this->event = $event;
    }

    public function render(): View
    {
        return view('livewire.dashboard.layouts.my-layout');
    }
}
```

### Component Features

**Event Property**: The active event is passed to the layout. Use it to determine if the dashboard should display or show the "no event" page.

**Minimal Logic**: Layouts should be thin; most logic lives in the widgets. A layout just determines arrangement and visibility rules.

**Mounting**: The `mount()` method receives the event from the parent DashboardLayout component. Store it as a property for use in the view.

## Step 3: Create the Layout View

The view determines how widgets are arranged and displayed. Here's a structure for a standard layout:

```blade
<div class="p-6 space-y-6">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold">Field Day Dashboard</h1>
        <p class="text-gray-600 mt-2">
            Event: {{ $this->event?->name ?? 'No active event' }}
        </p>
    </div>

    <!-- Widget Grid: 12-column layout -->
    <div class="grid grid-cols-12 gap-4">
        @forelse ($this->getVisibleWidgets() as $widget)
            <div class="col-span-{{ config('dashboard.grid_sizes.' . $widget['size']) }}">
                <livewire:is
                    :component="$widget['component']"
                    :tv-mode="false"
                    :wire:key="'widget-' . $widget['id']"
                />
            </div>
        @empty
            <div class="col-span-12">
                <p class="text-center text-gray-500">No widgets available</p>
            </div>
        @endforelse
    </div>
</div>
```

### View Tips

**Grid Layout**: Dashboards use a 12-column responsive grid. Widgets span 3, 4, 6, or 12 columns based on their size.

**Widget Rendering**: Use `<livewire:is :component="...">` to dynamically render widget components from the registry.

**Visibility Filtering**: Layouts should only show widgets the user can access and has enabled.

**Responsive Adjustments**: Grid columns adapt on mobile (see responsive patterns in project docs).

## Step 4: Register in Configuration

Add your layout to `config/dashboard.php` in the `'layouts'` array:

```php
'my-layout' => [
    'name' => 'My Custom Layout',
    'component' => 'dashboard.layouts.my-layout',
    'customizable' => true, // false if layout doesn't allow widget customization
    'description' => 'Description of what this layout is for',
],
```

### Configuration Options

| Field | Description |
|-------|-------------|
| `name` | Display name in layout selector dropdown |
| `component` | Livewire component path (dot notation) |
| `customizable` | Whether users can hide/show widgets in this layout |
| `description` | Brief explanation of layout purpose |

## Step 5: Update the Main Dashboard Component

The `DashboardLayout` component (part of the main dashboard page) handles layout selection and routing. When you add a new layout, the system automatically includes it in the selector dropdown via the config.

No code changes needed—just add to config and it appears in the UI.

## Layout Patterns

### Pattern 1: Simple Widget Grid

Most layouts just arrange widgets in a responsive grid:

```blade
<div class="grid grid-cols-12 gap-4 p-6">
    @foreach ($this->getVisibleWidgets() as $widget)
        <div class="col-span-{{ config('dashboard.grid_sizes.' . $widget['size']) }}">
            <livewire:is :component="$widget['component']" />
        </div>
    @endforeach
</div>
```

### Pattern 2: TV Layout with Theme

TV layout uses a dark theme and larger text:

```blade
<div class="p-8 bg-tv-bg min-h-screen text-tv-text" data-theme="tvdashboard">
    <!-- Large hero metrics -->
    <div class="grid grid-cols-2 gap-8 mb-12">
        <div class="col-span-1">
            <livewire:is
                :component="config('dashboard.widgets.qso-count.component')"
                :tv-mode="true"
            />
        </div>
        <div class="col-span-1">
            <livewire:is
                :component="config('dashboard.widgets.score.component')"
                :tv-mode="true"
            />
        </div>
    </div>

    <!-- Supporting widgets -->
    <div class="grid grid-cols-4 gap-8">
        @foreach ($this->getTvWidgets() as $widget)
            <livewire:is :component="$widget['component']" :tv-mode="true" />
        @endforeach
    </div>
</div>
```

### Pattern 3: Layout with Customizer Panel

For customizable layouts, include a widget customizer:

```blade
<div class="flex gap-6 p-6">
    <!-- Sidebar: Widget Customizer -->
    <div class="w-64 flex-shrink-0 border-r border-gray-200">
        <livewire:dashboard.widget-customizer :event="$this->event" />
    </div>

    <!-- Main: Widget Grid -->
    <div class="flex-1 grid grid-cols-12 gap-4">
        @foreach ($this->getCustomizableWidgets() as $widget)
            <div class="col-span-{{ config('dashboard.grid_sizes.' . $widget['size']) }}">
                <livewire:is :component="$widget['component']" />
            </div>
        @endforeach
    </div>
</div>
```

### Pattern 4: Mobile-Optimized Layout

For mobile, stack everything in a single column:

```blade
<div class="p-4 space-y-4">
    <!-- Full-width header -->
    <div class="bg-white rounded-lg p-4 shadow-sm">
        <h1 class="text-2xl font-bold">Dashboard</h1>
    </div>

    <!-- Single column widget stack -->
    <div class="space-y-4">
        @foreach ($this->getVisibleWidgets() as $widget)
            <livewire:is :component="$widget['component']" />
        @endforeach
    </div>
</div>
```

## Getting Widgets for Your Layout

### All Available Widgets

```php
config('dashboard.widgets')
```

### Filtered by Permission

```php
collect(config('dashboard.widgets'))
    ->filter(function ($widget) {
        $permission = $widget['permission'];
        return is_null($permission) || auth()->user()->can($permission);
    })
```

### Only TV-Visible Widgets

```php
collect(config('dashboard.widgets'))
    ->filter(fn ($w) => $w['tv_visible'] === true)
```

### Only Customizable Layouts

```php
collect(config('dashboard.layouts'))
    ->filter(fn ($l) => $l['customizable'] === true)
```

## Layout Features

### F-Key Toggle for TV Mode

TV layouts support toggling the navigation header with the F key. This is handled by the main DashboardLayout component via Alpine.js.

In your layout, add:

```blade
<div x-data="{ showNav: true }">
    <!-- Navigation (toggleable) -->
    <nav x-show="showNav" class="p-4 bg-gray-100">
        <!-- Navigation content -->
    </nav>

    <!-- Main content -->
    <div class="flex-1">
        <!-- Your layout content -->
    </div>
</div>
```

### Dark Theme Support

Use DaisyUI theme attributes for dark/light mode:

```blade
<!-- Default theme -->
<div class="bg-white text-gray-900">Content</div>

<!-- TV theme -->
<div class="bg-base-100 text-base-content" data-theme="tvdashboard">
    Content
</div>
```

### Real-Time Status Indicator

Show WebSocket connection status:

```blade
<div class="fixed bottom-4 right-4">
    <div
        x-data="{ connected: true }"
        @window:offline="connected = false"
        @window:online="connected = true"
        :class="connected ? 'bg-success' : 'bg-warning'"
        class="px-4 py-2 rounded-lg text-white text-sm"
    >
        {{ connected ? 'Live' : 'Offline' }}
    </div>
</div>
```

## Testing Your Layout

### Feature Test

```bash
php artisan make:test Feature/Dashboard/MyLayoutTest --no-interaction
```

```php
<?php

namespace Tests\Feature\Dashboard;

use App\Livewire\Dashboard\Layouts\MyLayout;
use App\Models\Event;
use Livewire\Livewire;
use Tests\TestCase;

class MyLayoutTest extends TestCase
{
    public function test_layout_renders(): void
    {
        $event = Event::factory()->create([
            'start_time' => now()->subHours(12),
            'end_time' => now()->addHours(12),
        ]);

        Livewire::test(MyLayout::class, ['event' => $event])
            ->assertViewIs('livewire.dashboard.layouts.my-layout');
    }

    public function test_layout_shows_widgets(): void
    {
        $event = Event::factory()->create([
            'start_time' => now()->subHours(12),
            'end_time' => now()->addHours(12),
        ]);

        Livewire::test(MyLayout::class, ['event' => $event])
            ->assertSeeLivewire('dashboard.widgets.qso-count');
    }
}
```

### Browser Test

```bash
php artisan make:test Browser/MyLayoutTest --no-interaction
```

```php
<?php

namespace Tests\Browser;

use App\Models\Event;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class MyLayoutTest extends DuskTestCase
{
    public function test_layout_renders_on_page(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->create([
            'start_time' => now()->subHours(12),
            'end_time' => now()->addHours(12),
        ]);

        $this->browse(function (Browser $browser) use ($user, $event) {
            $browser->loginAs($user)
                ->visit('/dashboard')
                ->assertSee('Field Day Dashboard');
        });
    }

    public function test_layout_switching_persists(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/dashboard')
                // Select a different layout
                ->select('layout', 'tv')
                ->pause(500)
                // Refresh and verify selection persisted
                ->refresh()
                ->assertSelected('layout', 'tv');
        });
    }
}
```

## Layout Switching Mechanism

The dashboard system handles layout switching automatically:

1. **User selects layout** from dropdown on dashboard page
2. **JavaScript saves to localStorage**: `localStorage.setItem('dashboard.layout', 'tv')`
3. **DashboardLayout component reads localStorage** on mount
4. **Correct layout component is rendered** based on selection
5. **Selection persists** across sessions

Your layout component doesn't need to handle this—the parent DashboardLayout component manages it.

## Widget Grid Sizing

The dashboard uses a 12-column responsive grid:

```blade
<!-- Small: 3 columns (25% width) -->
<div class="col-span-3">Widget</div>

<!-- Medium: 4 columns (33% width) -->
<div class="col-span-4">Widget</div>

<!-- Large: 6 columns (50% width) -->
<div class="col-span-6">Widget</div>

<!-- Full-width: 12 columns -->
<div class="col-span-12">Widget</div>
```

On mobile (sm breakpoint), responsive grid adapts:

```blade
<div class="grid grid-cols-12 sm:grid-cols-6 gap-4">
    <!-- Widget -->
</div>
```

## Responsive Design Best Practices

1. **Test at breakpoints**: 375px, 640px, 768px, 1024px, 1280px
2. **Stack on mobile**: Use single-column layout on small screens
3. **Match parent/child breakpoints**: If parent uses `lg:`, children should too
4. **Use `min-w-0` on flex children** to prevent overflow
5. **Use `truncate` on text** that might overflow

See `docs/responsive-patterns.md` for detailed responsive design guidelines.

## Complete Example: News Feed Layout

Here's a complete layout example:

**Component** (`app/Livewire/Dashboard/Layouts/NewsLayout.php`):

```php
<?php

namespace App\Livewire\Dashboard\Layouts;

use App\Models\Event;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class NewsLayout extends Component
{
    public ?Event $event = null;

    public function mount(?Event $event = null): void
    {
        $this->event = $event;
    }

    public function render(): View
    {
        return view('livewire.dashboard.layouts.news');
    }
}
```

**View** (`resources/views/livewire/dashboard/layouts/news.blade.php`):

```blade
<div class="space-y-6 p-6">
    <!-- Header -->
    <div class="border-b border-gray-200 pb-6">
        <h1 class="text-4xl font-bold">Field Day Dashboard</h1>
        <p class="text-gray-600 mt-2">{{ $this->event?->name }}</p>
    </div>

    <!-- Featured: Two hero widgets in a row -->
    <div class="grid grid-cols-12 gap-6">
        <div class="col-span-6">
            <livewire:is
                :component="config('dashboard.widgets.qso-count.component')"
                wire:key="widget-qso-count"
            />
        </div>
        <div class="col-span-6">
            <livewire:is
                :component="config('dashboard.widgets.score.component')"
                wire:key="widget-score"
            />
        </div>
    </div>

    <!-- Content: Remaining widgets -->
    <div class="grid grid-cols-12 gap-6">
        @forelse (collect(config('dashboard.widgets'))
            ->filter(fn ($w) => $w['default_visible'] && !$w['tv_visible'])
            as $widget)
            <div class="col-span-{{ config('dashboard.grid_sizes.' . $widget['size']) }}">
                <livewire:is
                    :component="$widget['component']"
                    wire:key="widget-{{ key($widget) }}"
                />
            </div>
        @empty
            <div class="col-span-12">
                <p class="text-center text-gray-500 py-12">
                    No widgets to display
                </p>
            </div>
        @endforelse
    </div>
</div>
```

**Config Entry** (`config/dashboard.php`):

```php
'news' => [
    'name' => 'News Feed Layout',
    'component' => 'dashboard.layouts.news',
    'customizable' => true,
    'description' => 'Featured widgets with feed-style layout',
],
```

## Checklist

Before considering your layout complete:

- [ ] Component created in `app/Livewire/Dashboard/Layouts/`
- [ ] View template created in `resources/views/livewire/dashboard/layouts/`
- [ ] Registered in `config/dashboard.php`
- [ ] Grid layout tested at multiple breakpoints
- [ ] Widgets render correctly in the layout
- [ ] Responsive design works on mobile
- [ ] Feature tests written
- [ ] Browser tests verify layout switching
- [ ] Accessibility checked (WCAG 2.1 AA)
- [ ] Documentation updated if adding special features

## Common Patterns

### Show/Hide Widgets Based on Permission

```blade
@forelse ($this->getVisibleWidgets() as $widget)
    @can($widget['permission'] ?? null)
        <div class="col-span-{{ config('dashboard.grid_sizes.' . $widget['size']) }}">
            <livewire:is :component="$widget['component']" />
        </div>
    @endcan
@empty
    <p class="text-gray-500">No widgets available</p>
@endforelse
```

### Conditional Widget Display

```blade
@if ($this->event && $this->event->isActive())
    <!-- Show dashboard -->
@else
    <!-- Show "no active event" message -->
@endif
```

### Custom Widget Ordering

```blade
@php
    $widgetOrder = ['qso-count', 'score', 'time-remaining', 'recent-contacts'];
@endphp

@foreach ($widgetOrder as $widgetKey)
    @if (config("dashboard.widgets.$widgetKey"))
        <div class="col-span-{{ config('dashboard.grid_sizes.' . config("dashboard.widgets.$widgetKey.size")) }}">
            <livewire:is
                :component="config('dashboard.widgets.' . $widgetKey . '.component')"
                wire:key="widget-{{ $widgetKey }}"
            />
        </div>
    @endif
@endforeach
```

## Troubleshooting

**Layout doesn't appear in dropdown?**
- Check `config/dashboard.php` for typos
- Verify the component path is correct (dot notation)
- Clear config cache: `php artisan config:clear`

**Widgets don't render?**
- Verify widget components exist at configured paths
- Check browser console for JavaScript errors
- Ensure `wire:key` is unique for each widget

**Layout switching doesn't work?**
- Verify localStorage is enabled in browser
- Check browser console for JavaScript errors
- Ensure you're setting `localStorage.setItem('dashboard.layout', 'layoutKey')`

**Responsive layout broken on mobile?**
- Test at actual mobile breakpoints (375px minimum)
- Use `sm:`, `md:`, `lg:` responsive classes
- Ensure grid columns sum correctly on smaller screens
