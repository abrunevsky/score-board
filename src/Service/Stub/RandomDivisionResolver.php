<?php

declare(strict_types=1);

namespace App\Service\Stub;

use App\Entity\Team;
use App\Service\TeamDivisionResolver;

class RandomDivisionResolver implements TeamDivisionResolver
{
    /**
     * @var string[]
     */
    private array $divisionNames;
    private int $playOffPlayers;

    /**
     * @param string[] $divisionNames
     */
    public function __construct(array $divisionNames = ['A', 'B'], int $playOffPlayers = 4)
    {
        $this->divisionNames = $divisionNames;
        $this->playOffPlayers = $playOffPlayers;
    }

    /**
     * @param Team[] $teams
     *
     * @return object{ pool: string[] }
     */
    public function createContext(array $teams): object
    {
        $divisionsCount = count($this->divisionNames);
        $minTeamsCount = $divisionsCount * $this->playOffPlayers;
        $teamsCount = count($teams);

        if ($teamsCount < $minTeamsCount) {
            throw new \LogicException(sprintf('Context creation exception: minimal number of teams is %d, but %d teams provided', $minTeamsCount, $teamsCount));
        }

        $divisionSize = (int) ceil($teamsCount / count($this->divisionNames));
        $pool = [];

        foreach ($this->divisionNames as $divisionName) {
            $pool[] = array_fill(0, $divisionSize, $divisionName);
        }

        $pool = array_merge(...$pool);
        shuffle($pool);

        return (object) compact('pool');
    }

    /**
     * @param object{ pool: string[] } $context
     */
    public function resolveDivision(Team $team, object $context): string
    {
        if (empty($context->pool)) {
            throw new \LogicException('Context is wrong or its pool is empty');
        }

        $divisions = array_splice($context->pool, array_rand($context->pool), 1);

        return (string) current($divisions);
    }
}
