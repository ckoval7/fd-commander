<?php

namespace App\Livewire\Dashboard\Widgets;

use App\Models\Contact;
use App\Models\Event;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ProgressGoals extends Component
{
    public bool $tvMode = false;

    public ?Event $event = null;

    // Configurable goals
    public int $qsoGoal = 1000;

    public int $scoreGoal = 5000;

    public function mount(bool $tvMode = false, int $qsoGoal = 1000, int $scoreGoal = 5000): void
    {
        $this->tvMode = $tvMode;
        $this->qsoGoal = $qsoGoal;
        $this->scoreGoal = $scoreGoal;
        $this->event = Event::active()->with('eventConfiguration')->first();
    }

    #[Computed]
    public function currentQsos(): int
    {
        if (! $this->event?->eventConfiguration) {
            return 0;
        }

        return Contact::where('event_configuration_id', $this->event->eventConfiguration->id)
            ->notDuplicate()
            ->count();
    }

    #[Computed]
    public function currentScore(): int
    {
        return $this->event?->eventConfiguration?->calculateFinalScore() ?? 0;
    }

    #[Computed]
    public function qsoProgress(): float
    {
        if ($this->qsoGoal === 0) {
            return 0;
        }

        return min(100, round(($this->currentQsos / $this->qsoGoal) * 100, 1));
    }

    #[Computed]
    public function scoreProgress(): float
    {
        if ($this->scoreGoal === 0) {
            return 0;
        }

        return min(100, round(($this->currentScore / $this->scoreGoal) * 100, 1));
    }

    #[Computed]
    public function qsoStatus(): string
    {
        return match (true) {
            $this->qsoProgress >= 100 => 'complete',
            $this->qsoProgress >= 75 => 'excellent',
            $this->qsoProgress >= 50 => 'good',
            $this->qsoProgress >= 25 => 'fair',
            default => 'behind',
        };
    }

    #[Computed]
    public function scoreStatus(): string
    {
        return match (true) {
            $this->scoreProgress >= 100 => 'complete',
            $this->scoreProgress >= 75 => 'excellent',
            $this->scoreProgress >= 50 => 'good',
            $this->scoreProgress >= 25 => 'fair',
            default => 'behind',
        };
    }

    public function render()
    {
        return view('livewire.dashboard.widgets.progress-goals');
    }
}
