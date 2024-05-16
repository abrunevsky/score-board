<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Championship;
use App\Entity\Play;
use App\Entity\PlayingTeam;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;

class ChampionshipFactory
{
    private TeamRepository $teamRepository;
    private EntityManagerInterface $entityManager;
    private TeamDivisionResolver $divisionResolver;
    private PlayingTimeResolver $playingTimeResolver;

    public function __construct(
        TeamRepository $teamRepository,
        EntityManagerInterface $entityManager,
        TeamDivisionResolver $divisionResolver,
        PlayingTimeResolver $playingPositionResolver
    ) {
        $this->teamRepository = $teamRepository;
        $this->entityManager = $entityManager;
        $this->divisionResolver = $divisionResolver;
        $this->playingTimeResolver = $playingPositionResolver;
    }

    public function create(bool $bidirectional): Championship
    {
        return $this->entityManager->wrapInTransaction(function () use ($bidirectional) {
            $championship = new Championship($bidirectional);

            $players = $this->prepareTeams($championship);
            $championship->setPlayingTeams($players);

            $plays = $this->preparePlays($championship, $players);
            $championship->setPlays($plays);

            $this->entityManager->persist($championship);
            $this->entityManager->flush();

            return $championship;
        });
    }

    /**
     * @return PlayingTeam[]
     */
    private function prepareTeams(Championship $championship): array
    {
        $players = [];
        $teams = $this->teamRepository->findAllSorted();
        $context = $this->divisionResolver->createContext($teams);
        foreach ($teams as $team) {
            $divisionCode = $this->divisionResolver->resolveDivision($team, $context);
            $playingTeam = new PlayingTeam($championship, $team, $divisionCode);
            $players[] = $playingTeam;
        }

        return $players;
    }

    /**
     * @param PlayingTeam[] $players
     *
     * @return array<int, Play>
     */
    private function preparePlays(Championship $championship, array $players): array
    {
        $plays = [];

        foreach ($players as $player1) {
            $skip = true;
            foreach ($players as $player2) {
                if ($player1->getDivision() !== $player2->getDivision()) {
                    continue;
                }

                if ($player1->getTeam()->getId() === $player2->getTeam()->getId()) {
                    $skip = false;
                    continue;
                }

                if ($skip) {
                    continue;
                }

                $plays[] = new Play(
                    $championship,
                    $player1->getTeam(),
                    $player2->getTeam(),
                    $this->playingTimeResolver->resolvePlayingTime(
                        $player1->getTeam(),
                        $player2->getTeam()
                    )
                );

                if ($championship->isBidirectional()) {
                    $plays[] = new Play(
                        $championship,
                        $player2->getTeam(),
                        $player1->getTeam(),
                        $this->playingTimeResolver->resolvePlayingTime(
                            $player2->getTeam(),
                            $player1->getTeam(),
                        )
                    );
                }
            }
        }

        return $plays;
    }
}
