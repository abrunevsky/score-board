<?php

declare(strict_types=1);

namespace App\Service\ProgressHandler;

use App\Entity\Championship;
use App\Entity\PlayOff;

class PlayOfFinalHandler extends AbstractPlayOfHandler
{
    public function canProcess(Championship $championship): bool
    {
        return Championship::STATUS_PLAYOFF_FINAL === $championship->getStatus();
    }

    public static function getIndex(): int
    {
        return 50;
    }

    protected static function getNextChampionshipStatus(): string
    {
        return Championship::STATUS_PLAYOFF_3RD_PLACE;
    }

    protected static function getCurrentStage(): string
    {
        return PlayOff::STAGE_FINAL;
    }
}
