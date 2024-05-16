<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Team;

interface PlayingTimeResolver
{
    public function resolvePlayingTime(Team $team1, Team $team2): \DateTimeImmutable;
}
