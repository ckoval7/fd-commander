<?php

namespace App\View\Components;

use App\Models\EventConfiguration;
use App\Models\Setting;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
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
        // Priority 1: System settings (from Settings page)
        $siteName = Setting::get('site_name');
        $siteTagline = Setting::get('site_tagline');
        $siteLogoPath = Setting::get('site_logo_path');

        // Priority 2: Get active event configuration
        $this->activeEvent = EventConfiguration::where('is_active', true)->first();

        // Set branding data with priority hierarchy
        // Logo: System settings > Event config > Default
        if ($siteLogoPath && Storage::disk('public')->exists($siteLogoPath)) {
            $this->logoPath = Storage::url($siteLogoPath);
        } elseif ($this->activeEvent && $this->activeEvent->logo_path) {
            $this->logoPath = $this->activeEvent->logo_path;
        } else {
            $this->logoPath = config('branding.default_logo', '/images/logo.svg');
        }

        // Site Name/Callsign: System settings > Event callsign > Default
        if ($siteName) {
            $this->callsign = $siteName;
        } elseif ($this->activeEvent) {
            $this->callsign = $this->activeEvent->callsign;
        } else {
            $this->callsign = config('branding.default_callsign', config('app.name'));
        }

        // Event Name: Active event > Site name > Default
        if ($this->activeEvent) {
            $this->eventName = $this->activeEvent->event->name ?? ($siteName ?: config('app.name'));
        } else {
            $this->eventName = $siteName ?: config('app.name');
        }

        // Tagline: System settings > Event tagline > Default
        if ($siteTagline) {
            $this->tagline = $siteTagline;
        } elseif ($this->activeEvent && $this->activeEvent->tagline) {
            $this->tagline = $this->activeEvent->tagline;
        } else {
            $this->tagline = config('branding.default_tagline');
        }
    }

    public function render(): View|Closure|string
    {
        return view('components.app-brand');
    }
}
