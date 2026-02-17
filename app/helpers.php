<?php

use App\Services\DeveloperClockService;
use Carbon\Carbon;

if (! function_exists('appNow')) {
    /**
     * Get the current application time, respecting developer mode time override.
     *
     * Use this instead of now() when you want the fake time to apply.
     * This does NOT affect system functions like CSRF, sessions, or cache.
     *
     * Examples of where to use appNow():
     * - Event countdown timers
     * - Event status calculations (upcoming, in_progress, completed)
     * - Display timestamps that should reflect the "fake" time
     *
     * Examples of where to use now():
     * - Audit logs (should always be real time)
     * - Database timestamps
     * - Anything security-related
     */
    function appNow(): Carbon
    {
        return app(DeveloperClockService::class)->now();
    }
}

if (! function_exists('formatTimeAgo')) {
    /**
     * Format a timestamp as a human-readable "time ago" string.
     *
     * @param  \Carbon\Carbon|string|null  $timestamp
     */
    function formatTimeAgo($timestamp): string
    {
        if (! $timestamp) {
            return 'never';
        }

        $time = $timestamp instanceof Carbon ? $timestamp : Carbon::parse($timestamp);
        $diffInSeconds = appNow()->diffInSeconds($time);

        if ($diffInSeconds < 60) {
            return 'just now';
        }

        $diffInMinutes = appNow()->diffInMinutes($time);
        if ($diffInMinutes < 60) {
            return $diffInMinutes.'m ago';
        }

        $diffInHours = appNow()->diffInHours($time);
        if ($diffInHours < 24) {
            return $diffInHours.'h ago';
        }

        $diffInDays = appNow()->diffInDays($time);

        return $diffInDays.'d ago';
    }
}
