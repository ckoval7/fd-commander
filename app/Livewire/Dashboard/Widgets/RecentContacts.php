<?php

namespace App\Livewire\Dashboard\Widgets;

use App\Livewire\Dashboard\Concerns\HasErrorBoundary;
use App\Models\Contact;
use App\Models\Event;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class RecentContacts extends Component
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
            unset($this->recentContacts);
        } catch (\Throwable $e) {
            $this->handleWidgetError($e);
        }
    }

    #[Computed]
    public function recentContacts(): Collection
    {
        if (! $this->event?->eventConfiguration) {
            return collect();
        }

        return Contact::where('event_configuration_id', $this->event->eventConfiguration->id)
            ->with(['band', 'mode', 'section', 'logger'])
            ->orderBy('qso_time', 'desc')
            ->limit(10)
            ->get();
    }

    protected function getWidgetName(): string
    {
        return 'Recent Contacts';
    }

    public function render()
    {
        if ($this->hasError) {
            return view('livewire.dashboard.widgets.error-fallback', [
                'widgetName' => $this->getWidgetName(),
                'errorMessage' => $this->errorMessage,
            ]);
        }

        return view('livewire.dashboard.widgets.recent-contacts');
    }
}
