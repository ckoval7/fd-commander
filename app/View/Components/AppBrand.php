<?php

namespace App\View\Components;

use App\Models\EventConfiguration;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AppBrand extends Component
{
    public ?EventConfiguration $activeEvent;

    public string $logoPath;

    public string $callsign;

    public string $eventName;

    public ?string $tagline;

    public function __construct()
    {
        // Get active event configuration
        $this->activeEvent = EventConfiguration::where('is_active', true)->first();

        // Set branding data
        if ($this->activeEvent) {
            $this->logoPath = $this->activeEvent->logo_path ?? config('branding.default_logo', '/images/logo.svg');
            $this->callsign = $this->activeEvent->callsign;
            $this->eventName = $this->activeEvent->event->name ?? config('app.name');
            $this->tagline = $this->activeEvent->tagline;
        } else {
            $this->logoPath = config('branding.default_logo', '/images/logo.svg');
            $this->callsign = config('branding.default_callsign', config('app.name'));
            $this->eventName = config('app.name');
            $this->tagline = config('branding.default_tagline');
        }
    }

    public function render(): View|Closure|string
    {
        return view('components.app-brand');
    }
}
