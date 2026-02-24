<?php

namespace App\Livewire;

use App\Models\Band;
use App\Models\BonusType;
use App\Models\Contact;
use App\Models\Event;
use App\Models\EventConfiguration;
use App\Models\Mode;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Scoring extends Component
{
    public ?Event $event = null;

    public function mount(): void
    {
        $this->event = Event::active()
            ->with([
                'eventConfiguration.section',
                'eventConfiguration.operatingClass',
                'eventConfiguration.bonuses',
            ])
            ->first();
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
        $this->clearComputedCache();
    }

    /**
     * Get the active event configuration.
     */
    protected function config(): ?EventConfiguration
    {
        return $this->event?->eventConfiguration;
    }

    // ========================================================================
    // SCORE TOTALS
    // ========================================================================

    #[Computed]
    public function qsoBasePoints(): int
    {
        if (! $this->config()) {
            return 0;
        }

        return (int) $this->config()->contacts()
            ->where('is_duplicate', false)
            ->sum('points');
    }

    #[Computed]
    public function powerMultiplier(): int
    {
        return $this->config()?->calculatePowerMultiplier() ?? 1;
    }

    #[Computed]
    public function qsoScore(): int
    {
        return $this->config()?->calculateQsoScore() ?? 0;
    }

    #[Computed]
    public function bonusScore(): int
    {
        return $this->config()?->calculateBonusScore() ?? 0;
    }

    #[Computed]
    public function finalScore(): int
    {
        return $this->config()?->calculateFinalScore() ?? 0;
    }

    // ========================================================================
    // QSO BREAKDOWN
    // ========================================================================

    #[Computed]
    public function totalContacts(): int
    {
        if (! $this->config()) {
            return 0;
        }

        return $this->config()->contacts()->count();
    }

    #[Computed]
    public function validContacts(): int
    {
        if (! $this->config()) {
            return 0;
        }

        return $this->config()->contacts()
            ->where('is_duplicate', false)
            ->count();
    }

    #[Computed]
    public function duplicateCount(): int
    {
        if (! $this->config()) {
            return 0;
        }

        return $this->config()->contacts()
            ->where('is_duplicate', true)
            ->count();
    }

    #[Computed]
    public function duplicateRate(): float
    {
        $total = $this->totalContacts;

        if ($total === 0) {
            return 0.0;
        }

        return round(($this->duplicateCount / $total) * 100, 1);
    }

    #[Computed]
    public function gotaContactCount(): int
    {
        if (! $this->config()) {
            return 0;
        }

        return $this->config()->contacts()
            ->where('is_gota_contact', true)
            ->count();
    }

    #[Computed]
    public function zeroPointContactCount(): int
    {
        if (! $this->config()) {
            return 0;
        }

        return $this->config()->contacts()
            ->where('is_duplicate', false)
            ->where('points', 0)
            ->count();
    }

    // ========================================================================
    // BAND/MODE GRID
    // ========================================================================

    #[Computed]
    public function bandModeGrid(): array
    {
        if (! $this->config()) {
            return [];
        }

        // Single aggregation query grouped by mode_id and band_id
        $counts = Contact::where('event_configuration_id', $this->config()->id)
            ->notDuplicate()
            ->selectRaw('band_id, mode_id, count(*) as contact_count, sum(points) as total_points')
            ->groupBy('band_id', 'mode_id')
            ->get()
            ->groupBy('mode_id');

        $data = [];

        foreach ($this->modes as $mode) {
            $modeCounts = $counts->get($mode->id, collect());
            $cells = [];
            $totalCount = 0;
            $totalPoints = 0;

            foreach ($this->bands as $band) {
                $entry = $modeCounts->firstWhere('band_id', $band->id);
                $count = $entry ? (int) $entry->contact_count : 0;
                $cells[$band->id] = $count;
                $totalCount += $count;
                $totalPoints += $entry ? (int) $entry->total_points : 0;
            }

            $data[] = [
                'mode' => $mode,
                'cells' => $cells,
                'total_count' => $totalCount,
                'total_points' => $totalPoints,
            ];
        }

        return $data;
    }

    // ========================================================================
    // BONUS LIST
    // ========================================================================

    #[Computed]
    public function bonusList(): array
    {
        $eventTypeId = $this->event?->event_type_id;

        $query = BonusType::where('is_active', true);
        if ($eventTypeId) {
            $query->where('event_type_id', $eventTypeId);
        }
        $bonusTypes = $query->get();

        $claimedBonuses = $this->config()
            ? $this->config()->bonuses->keyBy('bonus_type_id')
            : collect();

        $list = [];

        foreach ($bonusTypes as $bonusType) {
            $eventBonus = $claimedBonuses->get($bonusType->id);

            if ($eventBonus && $eventBonus->is_verified) {
                $status = 'verified';
                $points = (int) $eventBonus->calculated_points;
            } elseif ($eventBonus) {
                $status = 'claimed';
                $points = (int) $eventBonus->calculated_points;
            } else {
                $status = 'unclaimed';
                $points = 0;
            }

            $list[] = [
                'type' => $bonusType,
                'bonus' => $eventBonus,
                'status' => $status,
                'points' => $points,
            ];
        }

        return $list;
    }

    // ========================================================================
    // POWER SOURCES
    // ========================================================================

    /**
     * @return array<string, array{label: string, active: bool}>
     */
    #[Computed]
    public function powerSources(): array
    {
        $config = $this->config();

        return [
            'commercial' => ['label' => 'Commercial Power', 'active' => (bool) $config?->uses_commercial_power],
            'generator' => ['label' => 'Generator', 'active' => (bool) $config?->uses_generator],
            'battery' => ['label' => 'Battery', 'active' => (bool) $config?->uses_battery],
            'solar' => ['label' => 'Solar', 'active' => (bool) $config?->uses_solar],
            'wind' => ['label' => 'Wind', 'active' => (bool) $config?->uses_wind],
            'water' => ['label' => 'Water', 'active' => (bool) $config?->uses_water],
            'methane' => ['label' => 'Methane', 'active' => (bool) $config?->uses_methane],
        ];
    }

    // ========================================================================
    // POWER MULTIPLIER REASON
    // ========================================================================

    #[Computed]
    public function powerMultiplierReason(): string
    {
        $config = $this->config();

        if (! $config) {
            return 'No active event configuration.';
        }

        $watts = $config->max_power_watts;

        if ($watts > 100) {
            return "Operating at {$watts}W (over 100W) gives a 1\u{00d7} multiplier.";
        }

        $hasNaturalPower = $config->uses_battery
            || $config->uses_solar
            || $config->uses_wind
            || $config->uses_water;

        $hasDisqualifyingPower = $config->uses_commercial_power || $config->uses_generator;

        if ($watts <= 5 && $hasNaturalPower && ! $hasDisqualifyingPower) {
            return "Operating at {$watts}W with natural power and no commercial/generator power qualifies for the 5\u{00d7} QRP natural power bonus.";
        }

        if ($watts <= 5 && $hasDisqualifyingPower) {
            return "Operating at {$watts}W (QRP) gives a 2\u{00d7} multiplier. Switch to natural power only to qualify for 5\u{00d7}.";
        }

        if ($watts <= 5) {
            return "Operating at {$watts}W (QRP) gives a 2\u{00d7} multiplier.";
        }

        return "Operating at {$watts}W (6\u{2013}100W) gives a 2\u{00d7} multiplier.";
    }

    // ========================================================================
    // NOTICES
    // ========================================================================

    /**
     * @return array<int, array{severity: string, section: string, message: string}>
     */
    #[Computed]
    public function notices(): array
    {
        if (! $this->config()) {
            return [];
        }

        $notices = [];

        // High duplicate rate warning
        $rate = $this->duplicateRate;
        if ($rate > 5) {
            $notices[] = [
                'severity' => 'warning',
                'section' => 'qso',
                'message' => "High duplicate rate ({$rate}%) \u{2014} review log for callsign entry errors.",
            ];
        }

        // Zero-point contacts error
        $zeroCount = $this->zeroPointContactCount;
        if ($zeroCount > 0) {
            $notices[] = [
                'severity' => 'error',
                'section' => 'qso',
                'message' => "{$zeroCount} contact(s) logged with 0 points \u{2014} check band/mode assignment.",
            ];
        }

        // GOTA contacts without GOTA station
        $gotaCount = $this->gotaContactCount;
        if ($gotaCount > 0 && ! $this->config()->has_gota_station) {
            $notices[] = [
                'severity' => 'error',
                'section' => 'qso',
                'message' => "{$gotaCount} GOTA contact(s) logged but no GOTA station is configured.",
            ];
        }

        // Unverified claimed bonuses
        $unverifiedCount = collect($this->bonusList)
            ->filter(fn ($b) => $b['status'] === 'claimed')
            ->count();
        if ($unverifiedCount > 0) {
            $notices[] = [
                'severity' => 'warning',
                'section' => 'bonus',
                'message' => "{$unverifiedCount} bonus(es) claimed but not yet verified.",
            ];
        }

        // Satellite contacts exist but satellite_qso bonus unclaimed
        $hasSatelliteContacts = $this->config()->contacts()
            ->where('is_satellite', true)
            ->exists();
        $satelliteBonusStatus = collect($this->bonusList)
            ->first(fn ($b) => $b['type']->code === 'satellite_qso');
        if ($hasSatelliteContacts && $satelliteBonusStatus && $satelliteBonusStatus['status'] === 'unclaimed') {
            $notices[] = [
                'severity' => 'opportunity',
                'section' => 'bonus',
                'message' => 'Satellite contacts detected but satellite QSO bonus has not been claimed.',
            ];
        }

        // QRP power opportunity: <=5W but only 2x because no natural power
        $watts = $this->config()->max_power_watts;
        $multiplier = $this->powerMultiplier;
        if ($watts <= 5 && $multiplier === 2) {
            $hasAnyNaturalPower = $this->config()->uses_battery
                || $this->config()->uses_solar
                || $this->config()->uses_wind
                || $this->config()->uses_water;
            if (! $hasAnyNaturalPower) {
                $notices[] = [
                    'severity' => 'opportunity',
                    'section' => 'power',
                    'message' => "Running {$watts}W QRP \u{2014} add a natural power source (battery, solar, wind, water) to qualify for the 5\u{00d7} multiplier.",
                ];
            }
        }

        return $notices;
    }

    // ========================================================================
    // REFERENCE DATA
    // ========================================================================

    #[Computed]
    public function bands(): Collection
    {
        return Band::allowedForFieldDay()->ordered()->get();
    }

    #[Computed]
    public function modes(): Collection
    {
        return Mode::orderBy('name')->get();
    }

    // ========================================================================
    // CACHE MANAGEMENT
    // ========================================================================

    /**
     * Clear all computed property caches.
     */
    public function clearComputedCache(): void
    {
        unset(
            $this->qsoBasePoints,
            $this->powerMultiplier,
            $this->qsoScore,
            $this->bonusScore,
            $this->finalScore,
            $this->totalContacts,
            $this->validContacts,
            $this->duplicateCount,
            $this->duplicateRate,
            $this->gotaContactCount,
            $this->zeroPointContactCount,
            $this->bandModeGrid,
            $this->bonusList,
            $this->powerSources,
            $this->powerMultiplierReason,
            $this->notices,
            $this->bands,
            $this->modes,
        );
    }

    public function render()
    {
        return view('livewire.scoring');
    }
}
