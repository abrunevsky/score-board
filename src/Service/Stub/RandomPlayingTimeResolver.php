<?php

declare(strict_types=1);

namespace App\Service\Stub;

use App\Entity\Team;
use App\Service\PlayingTimeResolver;

class RandomPlayingTimeResolver implements PlayingTimeResolver
{
    public function resolvePlayingTime(Team $team): \DateTimeImmutable
    {
        return (new \DateTimeImmutable())->modify(
            sprintf(
                '+ %d days',
                \random_int(1, $team->getId() + strlen($team->getName()))
            )
        );
    }
}
