<?php

namespace App\Livewire\Dashboard;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class LayoutSelector extends Component
{
    public string $selectedLayout = 'default';

    public array $layouts = [];

    public function mount(): void
    {
        $this->layouts = $this->getAvailableLayouts();
    }

    protected function getAvailableLayouts(): array
    {
        return collect(config('dashboard.layouts', []))
            ->map(fn ($config, $key) => [
                'key' => $key,
                'name' => $config['name'] ?? ucfirst($key),
                'description' => $config['description'] ?? '',
            ])
            ->values()
            ->toArray();
    }

    public function switchLayout(string $layout): void
    {
        // Validate layout exists
        if (! array_key_exists($layout, config('dashboard.layouts', []))) {
            return;
        }

        // TV layout is a separate page
        if ($layout === 'tv') {
            $this->redirect(route('dashboard.tv'));

            return;
        }

        $this->selectedLayout = $layout;
        $this->dispatch('layout-changed', layout: $layout);
    }

    public function render(): View
    {
        return view('livewire.dashboard.layout-selector');
    }
}
