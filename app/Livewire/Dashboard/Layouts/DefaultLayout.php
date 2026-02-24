<?php

namespace App\Livewire\Dashboard\Layouts;

use App\Models\Event;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class DefaultLayout extends Component
{
    public ?Event $event = null;

    public function mount(?Event $event = null): void
    {
        $this->event = $event;
    }

    public function render(): View
    {
        return view('livewire.dashboard.layouts.default');
    }
}
