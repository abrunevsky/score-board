<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Championship;

class ChampionshipProcessor
{
    /**
     * @todo: Implement method that will update Championship state step by step.
     */
    public function processStep(Championship $championship): void
    {
    }

    /**
     * @todo: Implement method that will update Championship to the final state (status=finished).
     */
    public function processAllSteps(Championship $championship): void
    {
    }
}
