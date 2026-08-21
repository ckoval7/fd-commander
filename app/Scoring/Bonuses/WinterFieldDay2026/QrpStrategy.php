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
 * Configured power is per-station rather than per-mode here, so the stricter
 * 5 W ceiling is applied whenever the operation logged any CW or Digital
 * contact, and the 10 W Phone ceiling otherwise. Judging against the looser
 * ceiling would award the objective to an operation the rules exclude.
 */
class QrpStrategy extends AbstractBonusStrategy
{
    private const PHONE_WATT_CEILING = 10;

    private const CW_DIGITAL_WATT_CEILING = 5;

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
        $achieved = $config->hasContacts()
            && $config->effectiveMaxPowerWatts() <= $this->wattCeilingFor($config);

        $this->writeOrDelete($config, $this->bonusTypeFor($config), $achieved ? 1 : 0, 0);
    }

    /**
     * The ceiling this operation must stay under, given the modes it worked.
     */
    private function wattCeilingFor(EventConfiguration $config): int
    {
        $usedCwOrDigital = $config->contacts()
            ->where('is_duplicate', false)
            ->whereHas('mode', fn ($q) => $q->whereIn('category', ['CW', 'Digital']))
            ->exists();

        return $usedCwOrDigital ? self::CW_DIGITAL_WATT_CEILING : self::PHONE_WATT_CEILING;
    }
}
