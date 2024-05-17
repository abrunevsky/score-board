<?php

declare(strict_types=1);

namespace App\Service\ProgressHandler;

use App\Entity\Championship;
use App\Entity\PlayOff;

class PlayOf3rdPlaceHandler extends AbstractPlayOfHandler
{
    public function canProcess(Championship $championship): bool
    {
        return Championship::STATUS_PLAYOFF_3RD_PLACE === $championship->getStatus();
    }

    public static function getIndex(): int
    {
        return 60;
    }

    protected static function getNextChampionshipStatus(): string
    {
        return Championship::STATUS_FINISHED;
    }

    protected static function getCurrentStage(): string
    {
        return PlayOff::STAGE_3RD_PLACE;
    }
}
