# Creating Dashboard Widgets

This guide walks you through creating a new dashboard widget from start to finish.

## Quick Start

A widget is a self-contained Livewire component that displays real-time data in the dashboard. Each widget:
- Fetches its own data
- Handles real-time updates via Reverb
- Respects user permissions
- Supports different sizes and layouts
- Includes error handling

## Step 1: Create the Livewire Component

First, generate a new Livewire component in the `Dashboard\Widgets` namespace:

```bash
php artisan make:livewire Dashboard/Widgets/MyNewWidget --no-interaction
```

This creates:
- `app/Livewire/Dashboard/Widgets/MyNewWidget.php` (component class)
- `resources/views/livewire/dashboard/widgets/my-new-widget.blade.php` (view)

## Step 2: Build the Component Class

Here's a template for a basic widget:

```php
<?php

namespace App\Livewire\Dashboard\Widgets;

use App\Livewire\Dashboard\Concerns\HasErrorBoundary;
use App\Models\Event;
use Livewire\Attributes\Computed;
use Livewire\Component;

class MyNewWidget extends Component
{
    use HasErrorBoundary;

    public bool $tvMode = false;

    public ?Event $event = null;

    public function mount(bool $tvMode = false): void
    {
        $this->tvMode = $tvMode;
        $this->event = Event::active()->with('eventConfiguration')->first();
    }

    /**
     * @return array<string, string>
     */
    public function getListeners(): array
    {
        if (! $this->event) {
            return [];
        }

        return [
            "echo-private:event.{$this->event->id},ContactLogged" => 'handleContactLogged',
        ];
    }

    /**
     * Handle real-time ContactLogged broadcast.
     *
     * @param  array<string, mixed>  $payload
     */
    public function handleContactLogged(array $payload): void
    {
        try {
            // Refresh computed properties by unsetting them
            unset($this->myData);

            // Dispatch custom events if needed
            $this->dispatch('widget-updated', ['data' => 'value']);
        } catch (\Throwable $e) {
            $this->handleWidgetError($e);
        }
    }

    #[Computed]
    public function myData(): array
    {
        if (! $this->event?->eventConfiguration) {
            return [];
        }

        // Fetch and return your widget data
        return [
            'total' => 42,
            'recent' => 3,
        ];
    }

    protected function getWidgetName(): string
    {
        return 'My New Widget';
    }

    public function render()
    {
        if ($this->hasError) {
            return view('livewire.dashboard.widgets.error-fallback', [
                'widgetName' => $this->getWidgetName(),
                'errorMessage' => $this->errorMessage,
            ]);
        }

        return view('livewire.dashboard.widgets.my-new-widget');
    }
}
```

### Key Component Features

**Error Handling**: Use the `HasErrorBoundary` trait to gracefully handle errors. All exceptions in event handlers are caught and displayed to the user instead of breaking the widget.

**TV Mode Support**: The `$tvMode` property indicates when the widget is displayed in TV mode. Use this to adjust styling (larger fonts, simplified layout).

**Event Retrieval**: Always use `Event::active()` to get the current active event. This respects your dev mode time-travel settings via `appNow()`.

**Real-Time Updates**: Implement `getListeners()` and `handleContactLogged()` to subscribe to Reverb broadcasts. Unset computed properties to force recalculation.

**Computed Properties**: Use `#[Computed]` for data that should be cached but can be recalculated when dependencies change. This improves performance.

## Step 3: Create the Widget View

Create the Blade view file with your widget UI. Here's an example:

```blade
<div class="widget-container">
    <div class="widget-header flex items-center justify-between p-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">
            {{ 'My New Widget' }}
        </h3>
    </div>

    <div class="widget-body p-6">
        @if ($this->event && $this->myData)
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-600">Total</p>
                    <p class="{{ $this->tvMode ? 'text-5xl' : 'text-3xl' }} font-bold text-blue-600">
                        {{ $this->myData['total'] }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-600">Recent</p>
                    <p class="{{ $this->tvMode ? 'text-3xl' : 'text-2xl' }} font-semibold">
                        {{ $this->myData['recent'] }}
                    </p>
                </div>
            </div>
        @else
            <p class="text-center text-gray-500 py-8">
                No data available
            </p>
        @endif
    </div>
</div>
```

### View Tips

- **TV Mode**: Check `$tvMode` and increase font sizes accordingly using conditional classes
- **Responsive**: Use responsive Tailwind classes (e.g., `sm:text-2xl lg:text-3xl`)
- **Error Handling**: The `HasErrorBoundary` trait handles rendering, so just focus on your normal display
- **Data Availability**: Always check if `$this->event` and your data exist before displaying

## Step 4: Register in Configuration

Add your widget to `config/dashboard.php` in the `'widgets'` array:

```php
'my-new-widget' => [
    'name' => 'My New Widget',
    'component' => 'dashboard.widgets.my-new-widget',
    'icon' => 'chart-bar',
    'permission' => null, // null = available to all, or use a permission name
    'default_visible' => true,
    'tv_visible' => true, // false if not suitable for TV display
    'category' => 'analytics', // or 'scoring', 'event', 'activity', 'admin'
    'size' => 'medium', // small (3 cols), medium (4 cols), large (6 cols), full-width (12 cols)
],
```

### Configuration Options

| Field | Description |
|-------|-------------|
| `name` | Display name in widget customizer |
| `component` | Livewire component path (dot notation) |
| `icon` | Heroicon name (without prefix) |
| `permission` | Required permission for access, or `null` for public |
| `default_visible` | Shown by default in customizable layouts |
| `tv_visible` | Include in TV dashboard |
| `category` | For organizing in the widget customizer UI |
| `size` | Grid column span (small=3, medium=4, large=6, full=12) |

## Step 5: Add to Default Widget Order (Optional)

If you want the widget to appear by default, add it to the `'default_widget_order'` array in `config/dashboard.php`:

```php
'default_widget_order' => [
    'qso-count',
    'score',
    'my-new-widget',  // Add here
    'time-remaining',
    // ...
],
```

## Real-Time Updates with Reverb

Widgets automatically receive broadcast events when contacts are logged. Here's how it works:

### 1. Widget Subscribes to Broadcast

```php
public function getListeners(): array
{
    return [
        "echo-private:event.{$this->event->id},ContactLogged" => 'handleContactLogged',
    ];
}
```

### 2. Handle the Broadcast

```php
public function handleContactLogged(array $payload): void
{
    try {
        // Access payload data
        $callsign = $payload['callsign'];
        $band = $payload['band'];
        $mode = $payload['mode'];

        // Refresh computed properties
        unset($this->myData);

        // Dispatch custom events for visual feedback
        $this->dispatch('widget-updated', ['callsign' => $callsign]);
    } catch (\Throwable $e) {
        $this->handleWidgetError($e);
    }
}
```

### 3. Respond to Broadcast (Optional)

Use Alpine.js in your view to respond to custom events:

```blade
<div
    x-data="{ flash: false }"
    @widget-updated.window="flash = true; setTimeout(() => flash = false, 500)"
    :class="flash ? 'ring-4 ring-success animate-pulse' : ''"
>
    <!-- Your widget content -->
</div>
```

## Permission-Based Widgets

To restrict a widget to certain roles, set the `permission` in config:

```php
'equipment-status' => [
    // ...
    'permission' => 'view-equipment',
    // ...
],
```

Then in your component, check permissions before showing sensitive data:

```php
#[Computed]
public function equipmentStatus(): array
{
    if (! auth()->user()->can('view-equipment')) {
        return [];
    }

    return Equipment::get()->map(/* ... */)->toArray();
}
```

## TV Mode Guidelines

When designing for TV display (10+ feet away), follow these guidelines:

### Font Sizes
- Hero metrics: `text-9xl` (288px) for primary numbers
- Secondary metrics: `text-5xl` (48px) for supporting numbers
- Labels: `text-3xl` (30px) minimum

### Spacing
- Use 2-3x normal padding: `p-6` to `p-8`
- Generous line height: `leading-relaxed` or `leading-loose`
- Clear visual separation between sections

### Layout
- Single-screen display (no scrolling)
- High contrast: dark backgrounds with light text
- Remove interactive elements (buttons, forms)
- Hide or simplify labels

### Implementation

```blade
@if ($this->tvMode)
    <!-- TV-optimized layout with large text -->
    <div class="p-8 space-y-8">
        <div>
            <p class="text-3xl text-gray-400">QSO Count</p>
            <p class="text-9xl font-extrabold text-amber-400">{{ $qsoCount }}</p>
        </div>
    </div>
@else
    <!-- Normal layout -->
    <div class="p-4 space-y-4">
        <p class="text-sm text-gray-600">QSO Count</p>
        <p class="text-3xl font-bold">{{ $qsoCount }}</p>
    </div>
@endif
```

## Widget Size Grid

The dashboard uses a 12-column grid. Size defines how many columns a widget spans:

| Size | Columns | Use Case |
|------|---------|----------|
| `small` | 3 | Quick stats, status indicators |
| `medium` | 4 | Primary metrics (QSO count, score) |
| `large` | 6 | Detailed lists, charts, grids |
| `full-width` | 12 | Tables, full-width visualizations |

Layout adapts responsively on smaller screens.

## Example: Complete Weather Widget

Here's a complete widget example you can reference:

**Component** (`app/Livewire/Dashboard/Widgets/MyWeather.php`):

```php
<?php

namespace App\Livewire\Dashboard\Widgets;

use App\Livewire\Dashboard\Concerns\HasErrorBoundary;
use App\Models\Event;
use Livewire\Attributes\Computed;
use Livewire\Component;

class MyWeather extends Component
{
    use HasErrorBoundary;

    public bool $tvMode = false;
    public ?Event $event = null;

    public function mount(bool $tvMode = false): void
    {
        $this->tvMode = $tvMode;
        $this->event = Event::active()->with('eventConfiguration')->first();
    }

    #[Computed]
    public function weather(): array
    {
        return [
            'temp' => 72,
            'condition' => 'Partly Cloudy',
            'humidity' => 65,
        ];
    }

    protected function getWidgetName(): string
    {
        return 'Weather';
    }

    public function render()
    {
        if ($this->hasError) {
            return view('livewire.dashboard.widgets.error-fallback', [
                'widgetName' => $this->getWidgetName(),
                'errorMessage' => $this->errorMessage,
            ]);
        }

        return view('livewire.dashboard.widgets.my-weather');
    }
}
```

**View** (`resources/views/livewire/dashboard/widgets/my-weather.blade.php`):

```blade
<div class="rounded-lg border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-200 p-4">
        <h3 class="text-lg font-semibold">Weather</h3>
    </div>

    <div class="p-6 space-y-4">
        <div class="text-center">
            <p class="{{ $this->tvMode ? 'text-6xl' : 'text-4xl' }} font-bold">
                {{ $this->weather['temp'] }}°F
            </p>
            <p class="{{ $this->tvMode ? 'text-2xl' : 'text-sm' }} text-gray-600 mt-2">
                {{ $this->weather['condition'] }}
            </p>
        </div>

        <div class="pt-4 border-t border-gray-100">
            <p class="text-sm text-gray-600">Humidity</p>
            <p class="text-2xl font-semibold">{{ $this->weather['humidity'] }}%</p>
        </div>
    </div>
</div>
```

**Config Entry** (`config/dashboard.php`):

```php
'my-weather' => [
    'name' => 'Weather',
    'component' => 'dashboard.widgets.my-weather',
    'icon' => 'cloud',
    'permission' => null,
    'default_visible' => true,
    'tv_visible' => true,
    'category' => 'event',
    'size' => 'small',
],
```

## Testing Your Widget

### Unit Test

```bash
php artisan make:test Unit/Livewire/Dashboard/Widgets/MyNewWidgetTest --no-interaction
```

```php
<?php

namespace Tests\Unit\Livewire\Dashboard\Widgets;

use App\Livewire\Dashboard\Widgets\MyNewWidget;
use App\Models\Event;
use PHPUnit\Framework\TestCase;

class MyNewWidgetTest extends TestCase
{
    public function test_widget_displays_data(): void
    {
        $event = Event::factory()->create([
            'start_time' => now()->subHours(12),
            'end_time' => now()->addHours(12),
        ]);

        $component = new MyNewWidget();
        $component->event = $event;

        $data = $component->myData;

        $this->assertIsArray($data);
        $this->assertArrayHasKey('total', $data);
    }
}
```

### Feature Test

```bash
php artisan make:test Feature/Dashboard/MyNewWidgetTest --no-interaction
```

```php
<?php

namespace Tests\Feature\Dashboard;

use App\Livewire\Dashboard\Widgets\MyNewWidget;
use App\Models\Event;
use Livewire\Livewire;
use Tests\TestCase;

class MyNewWidgetTest extends TestCase
{
    public function test_widget_renders(): void
    {
        $event = Event::factory()->create([
            'start_time' => now()->subHours(12),
            'end_time' => now()->addHours(12),
        ]);

        Livewire::test(MyNewWidget::class)
            ->assertViewIs('livewire.dashboard.widgets.my-new-widget')
            ->assertSee('My New Widget');
    }
}
```

## Checklist

Before considering your widget complete:

- [ ] Component created with `HasErrorBoundary` trait
- [ ] View template created with responsive design
- [ ] TV mode support implemented
- [ ] Registered in `config/dashboard.php`
- [ ] Added to `default_widget_order` if appropriate
- [ ] Real-time listeners configured (if needed)
- [ ] Unit and feature tests written
- [ ] Error handling tested
- [ ] Responsive design tested on mobile and desktop
- [ ] TV mode rendering tested at 10+ feet

## Common Patterns

### Caching Data

```php
#[Computed]
public function cachedData(): array
{
    $cacheKey = "widget.{$this->event->id}.my-data";
    return cache()->remember($cacheKey, 60, function () {
        return $this->fetchData();
    });
}
```

### Conditional Display

```blade
@can('manage-bonuses')
    <!-- Show restricted content -->
@else
    <p class="text-gray-500">You don't have permission to view this widget.</p>
@endcan
```

### Loading State

```blade
<div wire:loading.delay class="text-center py-8">
    <x-mary-loading />
</div>

<div wire:loading.remove>
    <!-- Your content -->
</div>
```

## Troubleshooting

**Widget doesn't show up?**
- Check `config/dashboard.php` for typos in the component path
- Verify the Livewire component exists at `app/Livewire/Dashboard/Widgets/YourWidget.php`
- Check browser console for JavaScript errors

**Real-time updates not working?**
- Verify Reverb is enabled: `REVERB_ENABLED=true`
- Check browser console for WebSocket connection errors
- Ensure `getListeners()` returns the correct channel name
- Verify the broadcast event is being dispatched in your models

**Widget shows error in production?**
- Error messages are hidden in production for security
- Check server logs for detailed error messages
- Use `dd()` or logging in local environment to debug

**TV mode text too small?**
- Increase font sizes: use `text-6xl`, `text-7xl`, `text-8xl`, `text-9xl`
- Increase padding: use `p-6`, `p-8` instead of `p-2`, `p-4`
- Test with the actual distance your TV will be viewed from
