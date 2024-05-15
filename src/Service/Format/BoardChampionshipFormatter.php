<?php

declare(strict_types=1);

namespace App\Service\Format;

use App\Entity\Championship;
use App\Entity\PlayOff;

class BoardChampionshipFormatter
{
    /**
     * @return array<string, mixed>
     */
    public function format(Championship $championship): array
    {
        return [
            'id' => $championship->getId(),
            'status' => $championship->getStatus(),
            'divisions' => $this->formatDivisions($championship),
            'playoff' => $this->formatPlayOff($championship->getPlayOffs()),
        ];
    }

    /**
     * @return array<string, array<int, array<string, int|array<int, array{int, int}|null>>>>
     */
    private function formatDivisions(Championship $championship): array
    {
        $playingTeams = $championship->getSortedPlayingTeams();
        $plays = $championship->getPlays();
        $isBidirectional = $championship->isBidirectional();

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

            $divisions[$playingTeam->getDivision()][] = [
                'teamId' => $playingTeam->getTeam()->getId(),
                'scores' => $teamPlays[$playingTeam->getTeam()->getId()] ?? null,
                'total' => $playingTeam->getScore(),
            ];
        }

        ksort($divisions);

        return $divisions;
    }

    /**
     * @param PlayOff[] $getPlayOffs
     *
     * @return array<string, mixed>
     *
     * @todo: implement fetching data from repository and format them
     */
    private function formatPlayOff(array $getPlayOffs): array
    {
        return [];
    }
}
