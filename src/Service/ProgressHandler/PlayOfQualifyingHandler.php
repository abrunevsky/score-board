<?php

declare(strict_types=1);

namespace App\Service\ProgressHandler;

use App\Entity\Championship;
use App\Entity\PlayOff;

class PlayOfQualifyingHandler extends AbstractPlayOfHandler
{
    private const TEAMS_DIVISIONS_QTY = 2;
    private const PLAYOFF_TEAMS_QTY = 4;

    public function canProcess(Championship $championship): bool
    {
        return Championship::STATUS_QUALIFYING === $championship->getStatus();
    }

    public function process(Championship $championship): void
    {
        $this->entityManager->wrapInTransaction(function () use ($championship) {
            $nextPlays = $this->prepareQuarterPlays($championship);
            $championship->appendPlayOffs($nextPlays);
            $championship->setStatus(Championship::STATUS_PLAYOFF_QUARTER);
        });
    }

    private function prepareQuarterPlays(Championship $championship): ?array
    {
        $playOffPlayers = [];
        $winnersByDivision = [];
        foreach ($championship->getSortedPlayingTeams() as $team) {
            if (!array_key_exists($team->getDivision(), $winnersByDivision)) {
                $winnersByDivision[$team->getDivision()] = [];
            }

            if (count($winnersByDivision[$team->getDivision()]) < self::PLAYOFF_TEAMS_QTY) {
                $winnersByDivision[$team->getDivision()][] = $team->getTeam();
            }
        }

        if (self::TEAMS_DIVISIONS_QTY !== count($winnersByDivision)) {
            $championship->setErrorMessage(
                sprintf(
                    '%d divisions has been found, but %d was expected.',
                    count($winnersByDivision),
                    self::TEAMS_DIVISIONS_QTY,
                )
            );
        } else {
            $failedDivisions = array_filter($winnersByDivision, function (array $divisionWinners) {
                return self::PLAYOFF_TEAMS_QTY !== count($divisionWinners);
            });

            if ($failedDivisions) {
                $divisionName = array_key_first($failedDivisions);
                $championship->setErrorMessage(
                    sprintf(
                        'Division %s contains %d winners, but %d has been expected.',
                        $divisionName,
                        count($failedDivisions[$divisionName]),
                        self::PLAYOFF_TEAMS_QTY
                    )
                );
            }


            [$divisionA, $divisionB] = array_values($winnersByDivision);

            foreach ($divisionA as $k => $team1) {
                $team2 = $divisionB[count($divisionB) - $k - 1];

                $playOffPlayers[] = new PlayOff(
                    $championship,
                    $team1,
                    $team2,
                    PlayOff::STAGE_QUARTERFINAL,
                    $this->timeResolver->resolvePlayingTime($team1, $team2)
                );
            }
        }

        return $playOffPlayers;
    }

    public static function getIndex(): int
    {
        return 20;
    }
}
