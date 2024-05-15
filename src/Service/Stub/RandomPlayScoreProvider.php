<?php

declare(strict_types=1);

namespace App\Service\Stub;

use App\Entity\Team;
use App\Service\PlayScoreProvider;

class RandomPlayScoreProvider implements PlayScoreProvider
{
    public function getScore(Team $team1, Team $team2): array
    {
        $score1 = \random_int(0, 1);

        return [$score1, 1 - $score1];
    }
}
