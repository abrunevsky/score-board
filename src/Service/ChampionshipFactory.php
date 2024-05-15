<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Play;
use App\Entity\PlayingTeam;
use App\Entity\Championship;
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
            $this->entityManager->persist($championship);

            $players = $this->prepareTeams($championship);
            $this->preparePlays($championship, $players);
            $championship->setStatus(Championship::STATUS_PLAY);

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
        foreach ($teams as $team) {
            $divisionCode = $this->divisionResolver->resolveDivision($team);
            $playingTeam = new PlayingTeam($championship, $team, $divisionCode);
            $championship->getPlayingTeams()->add($playingTeam);
            $players[] = $playingTeam;
        }

        return $players;
    }

    /**
     * @param PlayingTeam[] $players
     */
    private function preparePlays(Championship $championship, array $players): void
    {
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

                $play = new Play(
                    $championship,
                    $player1->getTeam(),
                    $player2->getTeam(),
                    $this->playingTimeResolver->resolvePlayingTime($player1->getTeam())
                );
                $championship->getPlays()->add($play);

                if ($championship->isBidirectional()) {
                    $guestPlay = new Play(
                        $championship,
                        $player2->getTeam(),
                        $player1->getTeam(),
                        $this->playingTimeResolver->resolvePlayingTime($player2->getTeam())
                    );
                    $championship->getPlays()->add($guestPlay);
                }
            }
        }
    }
}
