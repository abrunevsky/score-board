<?php

declare(strict_types=1);

namespace App\Service\Format;

use App\Entity\Team;

class BoardTeamFormatter
{
    /**
     * @param Team[] $teams
     *
     * @return array<int, array<string, int|string>>
     */
    public function formatArray(array $teams): array
    {
        $formattedTeams = [];
        foreach ($teams as $k => $team) {
            $formattedTeams[$k] = $this->format($team);
        }

        return $formattedTeams;
    }

    /**
     * @return array<string, int|string>
     */
    public function format(Team $team): array
    {
        return [
            'id' => $team->getId(),
            'name' => $team->getName(),
        ];
    }
}
