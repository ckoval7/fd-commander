<?php

use Illuminate\Support\Facades\Broadcast;

/**
 * Dashboard event channel - authenticated users can listen for real-time updates.
 */
Broadcast::channel('event.{eventId}', function ($user, int $eventId) {
    return $user !== null;
});
