<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\Setting;
use Illuminate\Console\Command;

class ActivateEventByDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:activate-by-date';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically activate events based on date range';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Check if manual activation is enabled
        if (Setting::getBoolean('manual_activation', false)) {
            $this->info('Manual activation is enabled - skipping auto-activation');

            return self::SUCCESS;
        }

        // Find events that are currently in progress
        $event = Event::query()
            ->where('start_time', '<=', now())
            ->where('end_time', '>=', now())
            ->orderBy('created_at', 'asc')
            ->first();

        if ($event) {
            // Activate the event
            Setting::set('active_event_id', $event->id);
            Setting::set('manual_activation', false);

            $this->info("Auto-activated event: {$event->name}");

            return self::SUCCESS;
        }

        // No events match the current date range
        $this->info('No events match the current date range');

        // Clear active event if one was set
        $currentActiveEventId = Setting::get('active_event_id');
        if ($currentActiveEventId) {
            Setting::set('active_event_id', null);
            $this->info('Cleared active event');
        }

        return self::SUCCESS;
    }
}
