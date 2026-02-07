<?php

namespace App\Enums;

enum NotificationCategory: string
{
    case NewSection = 'new_section';
    case Guestbook = 'guestbook';
    case Photos = 'photos';
    case StationStatus = 'station_status';
    case QsoMilestone = 'qso_milestone';
    case Equipment = 'equipment';

    /**
     * Get the human-readable label for the category.
     */
    public function label(): string
    {
        return match ($this) {
            self::NewSection => 'New Section',
            self::Guestbook => 'Guestbook',
            self::Photos => 'Photos',
            self::StationStatus => 'Station Status',
            self::QsoMilestone => 'QSO Milestone',
            self::Equipment => 'Equipment',
        };
    }

    /**
     * Get the Heroicon name for the category.
     */
    public function icon(): string
    {
        return match ($this) {
            self::NewSection => 'o-globe-americas',
            self::Guestbook => 'o-book-open',
            self::Photos => 'o-photo',
            self::StationStatus => 'o-signal',
            self::QsoMilestone => 'o-trophy',
            self::Equipment => 'o-wrench-screwdriver',
        };
    }

    /**
     * Get the debounce window in seconds for the category.
     */
    public function debounceSeconds(): int
    {
        return match ($this) {
            self::NewSection => 0,
            self::Guestbook => 180,
            self::Photos => 300,
            self::StationStatus => 120,
            self::QsoMilestone => 0,
            self::Equipment => 300,
        };
    }

    /**
     * Get a description of what this notification category is about.
     */
    public function description(): string
    {
        return match ($this) {
            self::NewSection => 'When a new ARRL/RAC section is worked',
            self::Guestbook => 'New guestbook entries',
            self::Photos => 'New photos uploaded to gallery',
            self::StationStatus => 'Station becomes available or occupied',
            self::QsoMilestone => 'QSO count milestones (every 50)',
            self::Equipment => 'Equipment status changes',
        };
    }
}
