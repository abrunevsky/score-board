<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Team;

interface TeamDivisionResolver
{
    /**
     * @param Team[] $teams
     *
     * @return object
     */
    public function createContext(array $teams): object;

    public function resolveDivision(Team $team, object $context): string;
}
