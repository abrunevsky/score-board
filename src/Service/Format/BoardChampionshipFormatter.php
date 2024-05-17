<?php

declare(strict_types=1);

namespace App\Service\Format;

use App\Entity\Championship;
use App\Entity\PlayOff;
use App\Entity\Team;

class BoardChampionshipFormatter
{
    private const PLAY_OFF_STAGE_MAP = [
        PlayOff::STAGE_QUARTERFINAL => 'qf',
        PlayOff::STAGE_SEMIFINAL => 'sf',
        PlayOff::STAGE_FINAL => 'f',
        PlayOff::STAGE_3RD_PLACE => '3p',
    ];

    /**
     * @param Team[] $teams
     *
     * @return array<string, mixed>
     */
    public function format(Championship $championship, array $teams): array
    {
        $teamsDictionary = [];
        foreach ($teams as $team) {
            $teamsDictionary[$team->getId()] ??= [];
            $teamsDictionary[$team->getId()] = $team->getName();
        }

        $divisionDictionary = [];
        foreach ($championship->getPlayingTeams() as $playingTeam) {
            $divisionDictionary[$playingTeam->getTeam()->getId()] = $playingTeam->getDivision();
        }

        return [
            'id' => $championship->getId(),
            'status' => $championship->getStatus(),
            'divisions' => $this->formatDivisions($championship, $teamsDictionary),
            'playoff' => $this->formatPlayOff($championship->getPlayOffs(), $teamsDictionary, $divisionDictionary),
            'rateList' => $this->createRateList($championship, $teamsDictionary),
        ];
    }

    /**
     * @param array<int, string> $teamsDictionary
     *
     * @return array<string, array<int, array<string, int|array<int, array{int, int}|null>>>>
     */
    private function formatDivisions(Championship $championship, array $teamsDictionary): array
    {
        $playingTeams = $championship->getSortedPlayingTeams();
        $plays = $championship->getPlays();
        $isBidirectional = $championship->isBidirectional();

        $teamPlays = [];
        foreach ($plays as $play) {
            $host = $play->getHost();
            $guest = $play->getGuest();

            $teamPlays[$host->getId()] ??= [];
            $teamPlays[$host->getId()][$guest->getId()] = $play->isCompleted()
                ? [$play->getHostScore(), $play->getGuestScore()]
                : null
            ;

            if (!$isBidirectional) { // appends mirrored record to the output
                $teamPlays[$guest->getId()] ??= [];
                $teamPlays[$guest->getId()][$host->getId()] = $play->isCompleted()
                    ? [$play->getGuestScore(), $play->getHostScore()]
                    : null
                ;
            }
        }

        $divisions = [];

        foreach ($playingTeams as $playingTeam) {
            $divisions[$playingTeam->getDivision()] ??= [];
            $divisions[$playingTeam->getDivision()][] = [
                'teamId' => $playingTeam->getTeam()->getId(),
                'teamName' => $teamsDictionary[$playingTeam->getTeam()->getId()],
                'scores' => $teamPlays[$playingTeam->getTeam()->getId()] ?? null,
                'total' => $playingTeam->getScore(),
            ];
        }

        ksort($divisions);

        return $divisions;
    }

    /**
     * @param PlayOff[]          $playOffs
     * @param array<int, string> $teamsDictionary
     * @param array<int, string> $divisionsDictionary
     *
     * @return array<string, mixed>
     */
    private function formatPlayOff(array $playOffs, array $teamsDictionary, array $divisionsDictionary): array
    {
        $formattedMap = [];
        foreach ($playOffs as $playOff) {
            $key = self::PLAY_OFF_STAGE_MAP[$playOff->getStage()];
            $formattedMap[$key] ??= [];
            $formattedMap[$key][] = [
                'teams' => [
                    sprintf('%s /%s/', $teamsDictionary[$playOff->getTeam1()->getId()], $divisionsDictionary[$playOff->getTeam1()->getId()]),
                    sprintf('%s /%s/', $teamsDictionary[$playOff->getTeam2()->getId()], $divisionsDictionary[$playOff->getTeam2()->getId()]),
                ],
                'score' => $playOff->isCompleted()
                    ? [
                        $playOff->getTeam1Score(),
                        $playOff->getTeam2Score(),
                    ]
                    : null,
            ];
        }

        return $formattedMap;
    }

    /**
     * @param array<int, string> $teamsDictionary
     *
     * @return string[]
     */
    private function createRateList(Championship $championship, array $teamsDictionary): array
    {
        $list = [];

        if (Championship::STATUS_FINISHED === $championship->getStatus()) {
            foreach ($championship->getSortedPlayingTeams() as $playingTeam) {
                $list[] = $teamsDictionary[$playingTeam->getTeam()->getId()];
            }
        }

        return $list;
    }
}
