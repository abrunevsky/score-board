<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Team;

interface PlayScoreProvider
{
    /**
     * @param Team $team1
     * @param Team $team2
     *
     * @return array{int, int}
     */
    public function getScore(Team $team1, Team $team2): array;
}
