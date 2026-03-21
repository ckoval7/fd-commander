<?php

namespace App\Livewire\Dashboard\Widgets;

use App\Models\Event;
use Livewire\Attributes\Computed;
use Livewire\Component;

class TimeRemaining extends Component
{
    public bool $tvMode = false;

    public ?Event $event = null;

    public function mount(bool $tvMode = false): void
    {
        $this->tvMode = $tvMode;
        $this->event = Event::active()->first();
    }

    #[Computed]
    public function timeRemaining(): array
    {
        if (! $this->event || ! $this->event->end_time) {
            return [
                'total_seconds' => 0,
                'hours' => 0,
                'minutes' => 0,
                'seconds' => 0,
                'formatted' => '00:00:00',
                'percentage' => 0,
            ];
        }

        $now = appNow();
        $remaining = $now->diffInSeconds($this->event->end_time, false);

        // If event is over (remaining is negative), return zeros
        if ($remaining < 0) {
            return [
                'total_seconds' => 0,
                'hours' => 0,
                'minutes' => 0,
                'seconds' => 0,
                'formatted' => 'Event Ended',
                'percentage' => 100,
            ];
        }

        $hours = floor($remaining / 3600);
        $minutes = floor(($remaining % 3600) / 60);
        $seconds = $remaining % 60;

        // Calculate percentage of time elapsed
        $totalDuration = $this->event->start_time->diffInSeconds($this->event->end_time);
        $elapsed = $this->event->start_time->diffInSeconds($now);
        $percentage = $totalDuration > 0 ? round(($elapsed / $totalDuration) * 100, 1) : 0;

        return [
            'total_seconds' => $remaining,
            'hours' => $hours,
            'minutes' => $minutes,
            'seconds' => $seconds,
            'formatted' => sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds),
            'percentage' => $percentage,
        ];
    }

    #[Computed]
    public function eventStatus(): string
    {
        if (! $this->event) {
            return 'No Active Event';
        }

        $now = appNow();

        return match (true) {
            $now->lt($this->event->start_time) => 'Not Started',
            $now->gt($this->event->end_time) => 'Ended',
            default => 'In Progress',
        };
    }

    public function render()
    {
        return view('livewire.dashboard.widgets.time-remaining');
    }
}
