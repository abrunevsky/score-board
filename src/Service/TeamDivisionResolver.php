<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Team;

interface TeamDivisionResolver
{
    public function resolveDivision(Team $team): string;
}
