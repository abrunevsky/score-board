<?php

declare(strict_types=1);

namespace App\Service\ProgressHandler;

use App\Entity\Championship;
use App\Entity\PlayOff;
use App\Service\PlayingTimeResolver;
use Doctrine\ORM\EntityManagerInterface;

class PlayOfQualifyingHandler implements ChampionshipHandlerInterface
{
    private const TEAMS_DIVISIONS_QTY = 2;
    private const PLAYOFF_TEAMS_QTY = 4;

    private EntityManagerInterface $entityManager;
    private PlayingTimeResolver $timeResolver;

    public function __construct(EntityManagerInterface $entityManager, PlayingTimeResolver $timeResolver)
    {
        $this->entityManager = $entityManager;
        $this->timeResolver = $timeResolver;
    }

    public function canProcess(Championship $championship): bool
    {
        return Championship::STATUS_QUALIFYING === $championship->getStatus();
    }

    public function process(Championship $championship): void
    {
        $this->entityManager->wrapInTransaction(function () use ($championship) {
            $this->prepareQuarterPlays($championship);
            $championship->setStatus(Championship::STATUS_PLAYOFF_QUARTER);
        });
    }

    private function prepareQuarterPlays(Championship $championship): void
    {
        $playOffPlayers = [];
        $winnersByDivision = [];
        foreach ($championship->getSortedPlayingTeams() as $team) {
            $winnersByDivision[$team->getDivision()] ??= [];
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

        $championship->appendPlayOffs($playOffPlayers);
    }

    public static function getIndex(): int
    {
        return 20;
    }
}
