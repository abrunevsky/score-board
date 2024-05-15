<?php

declare(strict_types=1);

namespace App\Service\Format;

use App\Entity\Championship;
use App\Entity\Play;
use App\Entity\PlayingTeam;
use App\Entity\PlayOff;

class BoardChampionshipFormatter
{
    public function format(Championship $championship): array
    {
        return [
            'id' => $championship->getId(),
            'status' => $championship->getStatus(),
            'divisions' => $this->formatDivisions(
                $championship->getPlayingTeams()->toArray(),
                $championship->getPlays()->toArray(),
                $championship->isBidirectional(),
            ),
            'playoff' => $this->formatPlayOff($championship->getPlayOffs()->toArray()),
        ];
    }

    /**
     * @param PlayingTeam[] $playingTeams
     * @param Play[]        $plays
     *
     * @todo: Implement rows/column sorting depending on teams total score
     */
    private function formatDivisions(array $playingTeams, array $plays, bool $isBidirectional): array
    {
        $teamPlays = [];
        foreach ($plays as $play) {
            $host = $play->getHost();
            $guest = $play->getGuest();

            if (!array_key_exists($host->getId(), $teamPlays)) {
                $teamPlays[$host->getId()] = [];
            }

            $teamPlays[$host->getId()][$guest->getId()] = $play->isCompleted()
                ? [$play->getHostScore(), $play->getGuestScore()]
                : null
            ;

            if (!$isBidirectional) { // appends mirrored record to the output
                if (!array_key_exists($guest->getId(), $teamPlays)) {
                    $teamPlays[$guest->getId()] = [];
                }

                $teamPlays[$guest->getId()][$host->getId()] = $play->isCompleted()
                    ? [$play->getGuestScore(), $play->getHostScore()]
                    : null
                ;
            }
        }

        $divisions = [];

        foreach ($playingTeams as $playingTeam) {
            if (!array_key_exists($playingTeam->getDivision(), $divisions)) {
                $divisions[$playingTeam->getDivision()] = [];
            }

            $divisions[$playingTeam->getDivision()][$playingTeam->getTeam()->getId()] = $teamPlays[$playingTeam->getTeam()->getId()] ?? null;
        }

        return $divisions;
    }

    /**
     * @param PlayOff[] $getPlayOffs
     *
     * @todo: implement fetching data from repository and format them
     */
    private function formatPlayOff(array $getPlayOffs): array
    {
        return [];
    }
}
