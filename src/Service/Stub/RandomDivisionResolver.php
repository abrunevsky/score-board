<?php

declare(strict_types=1);

namespace App\Service\Stub;

use App\Entity\Team;
use App\Service\TeamDivisionResolver;

class RandomDivisionResolver implements TeamDivisionResolver
{
    public const AVAILABLE_DIVISIONS = ['A', 'B'];

    public function resolveDivision(Team $team): string
    {
        return self::AVAILABLE_DIVISIONS[random_int(0, 1)];
    }
}
