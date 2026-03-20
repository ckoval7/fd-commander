<?php

namespace App\Livewire\Components;

use App\Models\Event;
use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class EventCountdown extends Component
{
    public ?Event $event = null;

    public string $state = '';

    public int $pollingInterval = 60;

    public array $countdown = [];

    public string $timezoneLabel = '';

    public string $timezone = 'UTC';

    public ?int $targetTimestamp = null;

    public int $serverTimestamp = 0;

    public string $label = '';

    public string $badgeClass = '';

    public string $textClass = '';

    public function mount(): void
    {
        $this->updateComponent();
    }

    public function updateComponent(): void
    {
        $this->event = $this->getRelevantEvent();

        if (! $this->event) {
            return;
        }

        $this->state = $this->determineState();
        $this->calculateCountdown();
        $this->updateTimezone();
        $this->determinePollingInterval();
        $this->setStateStyles();
    }

    protected function getRelevantEvent(): ?Event
    {
        // Priority 1: Active event (currently in date range)
        $activeEvent = Event::active()->first();
        if ($activeEvent) {
            return $activeEvent;
        }

        // Check for recently completed event (within 4 weeks)
        $completed = Event::completed()->orderBy('end_time', 'desc')->first();
        $completedWithin4Weeks = $completed && abs(appNow()->diffInDays($completed->end_time, false)) <= 28;

        // Check for upcoming event
        $upcoming = Event::upcoming()->orderBy('start_time')->first();

        // If there's a recently completed event
        if ($completedWithin4Weeks) {
            // If there's an upcoming event within 4 weeks, show that instead
            if ($upcoming && abs(appNow()->diffInDays($upcoming->start_time, false)) < 28) {
                return $upcoming;
            }

            // Otherwise show the ended event
            return $completed;
        }

        // If no recently completed event, show upcoming event if it exists
        if ($upcoming) {
            return $upcoming;
        }

        return null;
    }

    protected function determineState(): string
    {
        if (! $this->event) {
            return '';
        }

        if ($this->event->start_time <= appNow() && $this->event->end_time >= appNow()) {
            return 'active';
        }

        if ($this->event->start_time > appNow()) {
            return 'upcoming';
        }

        if ($this->event->end_time < appNow()) {
            return 'ended';
        }

        return '';
    }

    protected function calculateCountdown(): void
    {
        if (! $this->event) {
            $this->countdown = [];

            return;
        }

        $targetTime = match ($this->state) {
            'upcoming' => $this->event->start_time,
            'active' => $this->event->end_time,
            'ended' => $this->event->end_time,
            default => null,
        };

        if (! $targetTime) {
            $this->countdown = [];

            return;
        }

        $now = appNow();
        $diff = $this->state === 'ended'
            ? $now->diff($targetTime)
            : $targetTime->diff($now);

        $this->countdown = [
            'days' => $diff->days,
            'hours' => $diff->h,
            'minutes' => $diff->i,
            'seconds' => $diff->s,
        ];

        $this->targetTimestamp = $targetTime ? (int) $targetTime->timestamp : null;
    }

    protected function updateTimezone(): void
    {
        // Use user's preferred timezone if authenticated, otherwise use system timezone
        $timezone = auth()->check() && auth()->user()->preferred_timezone
            ? auth()->user()->preferred_timezone
            : Setting::get('timezone', config('app.timezone'));

        $now = appNow();

        // Get timezone abbreviation (e.g., EST, EDT, PST, PDT)
        $this->timezoneLabel = $now->timezone($timezone)->format('T');
        $this->timezone = $timezone;
        $this->serverTimestamp = (int) $now->timestamp;
    }

    protected function determinePollingInterval(): void
    {
        // Slow poll for event state sync; clocks and countdown tick client-side via Alpine
        $this->pollingInterval = 30;
    }

    protected function setStateStyles(): void
    {
        match ($this->state) {
            'upcoming' => [
                $this->label = 'Starts in',
                $this->badgeClass = 'badge-info',
                $this->textClass = 'text-info',
            ],
            'active' => [
                $this->label = 'Ends in',
                $this->badgeClass = 'badge-success',
                $this->textClass = 'text-success',
            ],
            'ended' => [
                $this->label = 'Ended',
                $this->badgeClass = 'badge-neutral',
                $this->textClass = 'text-neutral',
            ],
            default => [
                $this->label = '',
                $this->badgeClass = '',
                $this->textClass = '',
            ],
        };
    }

    public function getFormattedCountdownProperty(): string
    {
        if (empty($this->countdown)) {
            return '';
        }

        $days = $this->countdown['days'];
        $hours = $this->countdown['hours'];
        $minutes = $this->countdown['minutes'];
        $seconds = $this->countdown['seconds'];

        if ($days > 0) {
            return "{$days}d {$hours}h {$minutes}m";
        }

        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }

        return "{$minutes}m {$seconds}s";
    }

    public function render(): View
    {
        return view('livewire.components.event-countdown');
    }
}
