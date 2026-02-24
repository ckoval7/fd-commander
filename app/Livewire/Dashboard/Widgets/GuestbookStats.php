<?php

namespace App\Livewire\Dashboard\Widgets;

use App\Models\Event;
use App\Models\GuestbookEntry;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Component;

class GuestbookStats extends Component
{
    public bool $tvMode = false;

    public ?Event $event = null;

    public bool $hasPermission = false;

    public function mount(bool $tvMode = false): void
    {
        $this->tvMode = $tvMode;
        $this->hasPermission = Gate::allows('manage-guestbook');

        if ($this->hasPermission) {
            $this->event = Event::active()->with('eventConfiguration')->first();
        }
    }

    #[Computed]
    public function totalVisitors(): int
    {
        if (! $this->hasPermission || ! $this->event?->eventConfiguration) {
            return 0;
        }

        return GuestbookEntry::where('event_configuration_id', $this->event->eventConfiguration->id)
            ->count();
    }

    #[Computed]
    public function inPersonVisitors(): int
    {
        if (! $this->hasPermission || ! $this->event?->eventConfiguration) {
            return 0;
        }

        return GuestbookEntry::where('event_configuration_id', $this->event->eventConfiguration->id)
            ->where('presence_type', GuestbookEntry::PRESENCE_TYPE_IN_PERSON)
            ->count();
    }

    #[Computed]
    public function onlineVisitors(): int
    {
        if (! $this->hasPermission || ! $this->event?->eventConfiguration) {
            return 0;
        }

        return GuestbookEntry::where('event_configuration_id', $this->event->eventConfiguration->id)
            ->where('presence_type', GuestbookEntry::PRESENCE_TYPE_ONLINE)
            ->count();
    }

    #[Computed]
    public function vipVisitors(): int
    {
        if (! $this->hasPermission || ! $this->event?->eventConfiguration) {
            return 0;
        }

        return GuestbookEntry::where('event_configuration_id', $this->event->eventConfiguration->id)
            ->whereIn('visitor_category', [
                GuestbookEntry::VISITOR_CATEGORY_ELECTED_OFFICIAL,
                GuestbookEntry::VISITOR_CATEGORY_ARRL_OFFICIAL,
            ])
            ->count();
    }

    public function render()
    {
        if (! $this->hasPermission) {
            return view('livewire.dashboard.widgets.guestbook-stats-restricted');
        }

        return view('livewire.dashboard.widgets.guestbook-stats');
    }
}
