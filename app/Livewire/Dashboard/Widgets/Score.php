<?php

namespace App\Livewire\Dashboard\Widgets;

use App\Livewire\Dashboard\Concerns\HasErrorBoundary;
use App\Models\Event;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Score extends Component
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
            unset($this->qsoScore, $this->bonusScore, $this->finalScore);
        } catch (\Throwable $e) {
            $this->handleWidgetError($e);
        }
    }

    #[Computed]
    public function qsoScore(): int
    {
        return $this->event?->eventConfiguration?->calculateQsoScore() ?? 0;
    }

    #[Computed]
    public function bonusScore(): int
    {
        return $this->event?->eventConfiguration?->calculateBonusScore() ?? 0;
    }

    #[Computed]
    public function finalScore(): int
    {
        return $this->event?->eventConfiguration?->calculateFinalScore() ?? 0;
    }

    #[Computed]
    public function powerMultiplier(): int
    {
        return $this->event?->eventConfiguration?->calculatePowerMultiplier() ?? 1;
    }

    protected function getWidgetName(): string
    {
        return 'Current Score';
    }

    public function render()
    {
        if ($this->hasError) {
            return view('livewire.dashboard.widgets.error-fallback', [
                'widgetName' => $this->getWidgetName(),
                'errorMessage' => $this->errorMessage,
            ]);
        }

        return view('livewire.dashboard.widgets.score');
    }
}
