<?php

namespace App\Scoring\Bonuses\WinterFieldDay2026;

use App\Models\EventConfiguration;
use App\Scoring\Bonuses\AbstractBonusStrategy;

/**
 * WFD 2026 objective "special_bulletin".
 *
 * The bulletin copy is submitted as supporting documentation rather than logged, so the objective is claimed by hand.
 */
class SpecialBulletinStrategy extends AbstractBonusStrategy
{
    public function code(): string
    {
        return 'special_bulletin';
    }

    public function triggerType(): string
    {
        return 'manual';
    }

    public function subscribesTo(): array
    {
        return [];
    }

    public function reconcile(EventConfiguration $config): void
    {
        // Manual objective — the UI writes the claim row directly.
    }
}
