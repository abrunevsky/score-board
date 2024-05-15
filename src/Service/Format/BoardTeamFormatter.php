<?php

declare(strict_types=1);

namespace App\Service\Format;

use App\Entity\Team;

class BoardTeamFormatter
{
    /**
     * @param Team[] $teams
     *
     * @return array<array>
     */
    public function format(array $teams): array
    {
        $formattedTeams = [];
        foreach ($teams as $k => $team) {
            $formattedTeams[$k] = $this->formatItem($team);
        }
        return $formattedTeams;
    }

    /**
     * @param Team $team
     *
     * @return array<string, mixed>
     */
    public function formatItem(Team $team): array
    {
        return [
            'id' => $team->getId(),
            'name' => $team->getName(),
        ];
    }
}
