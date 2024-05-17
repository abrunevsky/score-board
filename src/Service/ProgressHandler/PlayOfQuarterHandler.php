<?php

declare(strict_types=1);

namespace App\Service\ProgressHandler;

use App\Entity\Championship;
use App\Entity\PlayOff;

class PlayOfQuarterHandler extends AbstractPlayOfHandler implements NextStageAwareInterface
{
    private const EXPECTED_PLAYS_QTY = 4;

    public function canProcess(Championship $championship): bool
    {
        return Championship::STATUS_PLAYOFF_QUARTER === $championship->getStatus();
    }

    public function prepareNextStage(Championship $championship): void
    {
        $currentPlays = $this->getCurrentPlays($championship, self::EXPECTED_PLAYS_QTY);

        $nextPlays = [];
        while ($currentPlays) {
            $play1 = array_shift($currentPlays);
            $play2 = array_shift($currentPlays);

            $nextPlays[] = new PlayOff(
                $championship,
                $team11 = self::getWinner($play1),
                $team12 = self::getWinner($play2),
                PlayOff::STAGE_SEMIFINAL,
                $this->timeResolver->resolvePlayingTime($team11, $team12)
            );
        }

        $championship->appendPlayOffs($nextPlays);
    }

    public static function getIndex(): int
    {
        return 30;
    }

    protected static function getNextChampionshipStatus(): string
    {
        return Championship::STATUS_PLAYOFF_SEMIFINAL;
    }

    protected static function getCurrentStage(): string
    {
        return PlayOff::STAGE_QUARTERFINAL;
    }
}
