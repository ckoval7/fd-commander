<?php

namespace App\Services;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Builder;

class LogbookQueryBuilder
{
    /**
     * Build a query for browsing contacts with eager loading.
     */
    public function buildQuery(): Builder
    {
        return Contact::query()
            ->with([
                'band',
                'mode',
                'section',
                'logger',
                'operatingSession.station',
            ]);
    }

    /**
     * Filter by event configuration.
     */
    public function forEvent(Builder $query, int $eventConfigurationId): Builder
    {
        return $query->where('event_configuration_id', $eventConfigurationId);
    }

    /**
     * Filter by band.
     */
    public function forBand(Builder $query, ?int $bandId): Builder
    {
        if ($bandId === null) {
            return $query;
        }

        return $query->where('band_id', $bandId);
    }

    /**
     * Filter by mode.
     */
    public function forMode(Builder $query, ?int $modeId): Builder
    {
        if ($modeId === null) {
            return $query;
        }

        return $query->where('mode_id', $modeId);
    }

    /**
     * Filter by station (through operating session).
     */
    public function forStation(Builder $query, ?int $stationId): Builder
    {
        if ($stationId === null) {
            return $query;
        }

        return $query->whereHas('operatingSession', function (Builder $q) use ($stationId) {
            $q->where('station_id', $stationId);
        });
    }

    /**
     * Filter by operator (logger).
     */
    public function forOperator(Builder $query, ?int $userId): Builder
    {
        if ($userId === null) {
            return $query;
        }

        return $query->where('logger_user_id', $userId);
    }

    /**
     * Filter by time range.
     */
    public function forTimeRange(Builder $query, ?string $fromTime, ?string $toTime): Builder
    {
        if ($fromTime !== null) {
            $query->where('qso_time', '>=', $fromTime);
        }

        if ($toTime !== null) {
            $query->where('qso_time', '<=', $toTime);
        }

        return $query;
    }

    /**
     * Filter by callsign (partial match, case-insensitive).
     */
    public function forCallsign(Builder $query, ?string $callsign): Builder
    {
        if ($callsign === null || trim($callsign) === '') {
            return $query;
        }

        return $query->where('callsign', 'like', '%'.strtoupper(trim($callsign)).'%');
    }

    /**
     * Filter by section.
     */
    public function forSection(Builder $query, ?int $sectionId): Builder
    {
        if ($sectionId === null) {
            return $query;
        }

        return $query->where('section_id', $sectionId);
    }

    /**
     * Filter by duplicate status.
     *
     * @param  string|null  $duplicateFilter  'only' = duplicates only, 'exclude' = exclude duplicates, null = show all
     */
    public function forDuplicateStatus(Builder $query, ?string $duplicateFilter): Builder
    {
        if ($duplicateFilter === 'only') {
            return $query->where('is_duplicate', true);
        }

        if ($duplicateFilter === 'exclude') {
            return $query->where('is_duplicate', false);
        }

        return $query;
    }

    /**
     * Filter by transcription status.
     *
     * @param  string|null  $transcribedFilter  'only' = transcribed only, null = show all
     */
    public function forTranscribed(Builder $query, ?string $transcribedFilter): Builder
    {
        if ($transcribedFilter === 'only') {
            return $query->where('is_transcribed', true);
        }

        return $query;
    }

    /**
     * Apply chronological ordering (most recent first).
     */
    public function chronological(Builder $query): Builder
    {
        return $query->orderBy('qso_time', 'desc');
    }

    /**
     * Build a complete filtered query with all provided filters.
     *
     * @param  array{
     *     event_configuration_id: int,
     *     band_id?: ?int,
     *     mode_id?: ?int,
     *     station_id?: ?int,
     *     operator_id?: ?int,
     *     time_from?: ?string,
     *     time_to?: ?string,
     *     callsign?: ?string,
     *     section_id?: ?int,
     *     duplicate_filter?: ?string,
     *     transcribed_filter?: ?string
     * }  $filters
     */
    public function applyFilters(array $filters): Builder
    {
        $query = $this->buildQuery();

        $query = $this->forEvent($query, $filters['event_configuration_id']);
        $query = $this->forBand($query, $filters['band_id'] ?? null);
        $query = $this->forMode($query, $filters['mode_id'] ?? null);
        $query = $this->forStation($query, $filters['station_id'] ?? null);
        $query = $this->forOperator($query, $filters['operator_id'] ?? null);
        $query = $this->forTimeRange($query, $filters['time_from'] ?? null, $filters['time_to'] ?? null);
        $query = $this->forCallsign($query, $filters['callsign'] ?? null);
        $query = $this->forSection($query, $filters['section_id'] ?? null);
        $query = $this->forDuplicateStatus($query, $filters['duplicate_filter'] ?? null);
        $query = $this->forTranscribed($query, $filters['transcribed_filter'] ?? null);
        $query = $this->chronological($query);

        return $query;
    }
}
