<?php

namespace App\Livewire\Settings;

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class SiteBranding extends Component
{
    use WithFileUploads;

    public string $site_name = '';

    public ?string $site_tagline = null;

    public ?string $logo_path = null;

    public $new_logo;

    public string $primary_color = '#2563eb';

    public string $secondary_color = '#7c3aed';

    public ?string $footer_text = null;

    public function mount(): void
    {
        $this->site_name = Setting::get('site_name', 'Field Day Log Database');
        $this->site_tagline = Setting::get('site_tagline');
        $this->logo_path = Setting::get('site_logo_path');
        $this->primary_color = Setting::get('primary_color', '#2563eb');
        $this->secondary_color = Setting::get('secondary_color', '#7c3aed');
        $this->footer_text = Setting::get('site_footer_text');
    }

    public function updatedNewLogo(): void
    {
        $this->validate([
            'new_logo' => ['image', 'max:2048', 'mimes:png,jpg,jpeg,svg'],
        ]);
    }

    public function removeLogo(): void
    {
        if ($this->logo_path) {
            Storage::disk('public')->delete($this->logo_path);
            Setting::set('site_logo_path', '');
            $this->logo_path = null;
            $this->dispatch('notify', title: 'Success', description: 'Logo removed successfully.');
        }
    }

    public function save(): void
    {
        $this->validate([
            'site_name' => ['required', 'string', 'max:100'],
            'site_tagline' => ['nullable', 'string', 'max:200'],
            'new_logo' => ['nullable', 'image', 'max:2048', 'mimes:png,jpg,jpeg,svg'],
            'primary_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondary_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'footer_text' => ['nullable', 'string', 'max:500'],
        ]);

        Setting::set('site_name', $this->site_name);

        if ($this->site_tagline) {
            Setting::set('site_tagline', $this->site_tagline);
        }

        if ($this->new_logo) {
            // Delete old logo
            if ($this->logo_path) {
                Storage::disk('public')->delete($this->logo_path);
            }

            // Save new logo
            $extension = $this->new_logo->getClientOriginalExtension();
            $filename = 'logo-'.time().'.'.$extension;
            $path = $this->new_logo->storeAs('branding', $filename, 'public');

            Setting::set('site_logo_path', $path);
            $this->logo_path = $path;
            $this->new_logo = null;
        }

        Setting::set('primary_color', $this->primary_color);
        Setting::set('secondary_color', $this->secondary_color);

        if ($this->footer_text) {
            Setting::set('site_footer_text', $this->footer_text);
        }

        Setting::clearCache();

        $this->dispatch('notify', title: 'Success', description: 'Branding settings saved successfully.');
    }

    public function render()
    {
        return view('livewire.settings.site-branding');
    }
}
