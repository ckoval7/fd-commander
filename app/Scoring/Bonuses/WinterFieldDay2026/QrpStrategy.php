<?php

namespace App\Scoring\Bonuses\WinterFieldDay2026;

use App\Models\EventConfiguration;
use App\Scoring\Bonuses\AbstractBonusStrategy;
use App\Scoring\DomainEvents\QsoLogged;

/**
 * WFD 2026 objective "qrp". OM x4.
 *
 * SOP: "Operating on QRP means every station in your Winter Field Day operation
 * is using 10 watts or less on Phone or 5 watts or less on CW or Digital for the
 * entire time you choose to operate during the event."
 *
 * The two ceilings are independent: an operation running 10 W on Phone and 5 W
 * on CW satisfies both and earns the objective. Each contact is therefore judged
 * against the ceiling for its own mode, using the power actually recorded on the
 * QSO rather than a single figure for the whole operation.
 *
 * Contacts predating per-QSO power capture (or imported without it) have no
 * power_watts of their own; those fall back to the operation's configured
 * maximum, which cannot understate the power used.
 */
class QrpStrategy extends AbstractBonusStrategy
{
    private const PHONE_WATT_CEILING = 10;

    private const CW_DIGITAL_WATT_CEILING = 5;

    /** Mode categories held to the stricter CW/Digital ceiling. */
    private const STRICT_CEILING_CATEGORIES = ['CW', 'Digital'];

    public function code(): string
    {
        return 'qrp';
    }

    public function triggerType(): string
    {
        return 'derived';
    }

    public function subscribesTo(): array
    {
        return [QsoLogged::class];
    }

    public function reconcile(EventConfiguration $config): void
    {
        $achieved = $config->hasContacts() && $this->everyContactWithinItsCeiling($config);

        $this->writeOrDelete($config, $this->bonusTypeFor($config), $achieved ? 1 : 0, 0);
    }

    /**
     * Whether every non-duplicate contact respected its own mode's ceiling.
     *
     * A single contact over its ceiling disqualifies the operation, so this
     * stops at the first exceedance rather than tallying them.
     */
    private function everyContactWithinItsCeiling(EventConfiguration $config): bool
    {
        $fallbackWatts = $config->effectiveMaxPowerWatts();

        $exceeded = $config->contacts()
            ->where('is_duplicate', false)
            ->with('mode:id,category')
            ->get(['id', 'mode_id', 'power_watts'])
            ->contains(function ($contact) use ($fallbackWatts) {
                $watts = $contact->power_watts ?? $fallbackWatts;

                return $watts > $this->ceilingForCategory($contact->mode?->category);
            });

        return ! $exceeded;
    }

    /**
     * Ceiling for a mode category, defaulting to the stricter one when the
     * category is unknown so an unclassified mode cannot buy the objective.
     */
    private function ceilingForCategory(?string $category): int
    {
        if ($category === null) {
            return self::CW_DIGITAL_WATT_CEILING;
        }

        return in_array($category, self::STRICT_CEILING_CATEGORIES, true)
            ? self::CW_DIGITAL_WATT_CEILING
            : self::PHONE_WATT_CEILING;
    }
}
