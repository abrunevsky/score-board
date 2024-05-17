<?php

declare(strict_types=1);

namespace App\Service\ProgressHandler;

use App\Entity\Championship;
use App\Entity\PlayOff;
use App\Entity\Team;

class PlayOfSemifinalHandler extends AbstractPlayOfHandler implements NextStageAwareInterface
{
    private const EXPECTED_PLAYS_QTY = 2;

    public function canProcess(Championship $championship): bool
    {
        return Championship::STATUS_PLAYOFF_SEMIFINAL === $championship->getStatus();
    }

    public function prepareNextStage(Championship $championship): void
    {
        $currentPlays = $this->getCurrentPlays($championship, self::EXPECTED_PLAYS_QTY);

        $nextPlays = [];
        $play1 = array_shift($currentPlays);
        $play2 = array_shift($currentPlays);

        $nextPlays[] = new PlayOff(
            $championship,
            $team11 = self::getWinner($play1),
            $team12 = self::getWinner($play2),
            PlayOff::STAGE_FINAL,
            $this->timeResolver->resolvePlayingTime($team11, $team12)
        );

        $nextPlays[] = new PlayOff(
            $championship,
            $team21 = self::getLooser($play1),
            $team22 = self::getLooser($play2),
            PlayOff::STAGE_3RD_PLACE,
            $this->timeResolver->resolvePlayingTime($team21, $team22)
        );

        $championship->appendPlayOffs($nextPlays);
    }

    protected static function getLooser(PlayOff $play): Team
    {
        return ($play->getTeam1Score() < $play->getTeam2Score()) ? $play->getTeam1() : $play->getTeam2();
    }

    public static function getIndex(): int
    {
        return 40;
    }

    protected static function getNextChampionshipStatus(): string
    {
        return Championship::STATUS_PLAYOFF_FINAL;
    }

    protected static function getCurrentStage(): string
    {
        return PlayOff::STAGE_SEMIFINAL;
    }
}
