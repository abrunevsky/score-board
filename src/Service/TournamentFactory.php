<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Play;
use App\Entity\PlayingTeam;
use App\Entity\Tournament;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;

class TournamentFactory
{
    private const AVAILABLE_DIVISIONS = [
        PlayingTeam::DIVISION_A,
        PlayingTeam::DIVISION_B,
    ];

    private TeamRepository $teamRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        TeamRepository $teamRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->teamRepository = $teamRepository;
        $this->entityManager = $entityManager;
    }

    public function create(bool $bidirectional): Tournament
    {
        return $this->entityManager->wrapInTransaction(function () use ($bidirectional) {
            $tournament = new Tournament($bidirectional);
            $this->entityManager->persist($tournament);

            $players = $this->prepareTeams($tournament);

            $this->preparePlays($tournament, $players);

            $tournament->setStatus(Tournament::STATUS_DIVISION);
            $this->entityManager->flush();

            return $tournament;
        });
    }

    /**
     * @return PlayingTeam[]
     */
    private function prepareTeams(Tournament $tournament): array
    {
        $players = [];

        $teams = $this->teamRepository->findAll();
        $divisionCounters = array_fill_keys(self::AVAILABLE_DIVISIONS, 0);
        foreach ($teams as $team) {
            $divisionCode = $this->resolveTeamDivision();
            ++$divisionCounters[$divisionCode];
            $playingTeam = new PlayingTeam(
                $tournament,
                $team,
                $divisionCode,
                $divisionCounters[$divisionCode]
            );
            $this->entityManager->persist($playingTeam);
            $players[] = $playingTeam;
        }

        return $players;
    }

    private function resolveTeamDivision(): string
    {
        return self::AVAILABLE_DIVISIONS[random_int(0, 1)];
    }

    /**
     * @param PlayingTeam[] $players
     */
    private function preparePlays(Tournament $tournament, array $players): void
    {
        $orderList = array_keys(array_fill(0, count($players), 0));

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
                    $tournament,
                    $player1->getTeam(),
                    $player2->getTeam(),
                    $this->resolvePlayingOrder($orderList)
                );

                $this->entityManager->persist($play);

                if ($tournament->isBidirectional()) {
                    $guestPlay = new Play(
                        $tournament,
                        $player2->getTeam(),
                        $player1->getTeam(),
                        $this->resolvePlayingOrder($orderList)
                    );
                    $this->entityManager->persist($guestPlay);
                }
            }
        }
    }

    /**
     * @param array<int, int> $orderList
     */
    private function resolvePlayingOrder(array $orderList): int
    {
        $k = array_rand($orderList);

        return (int) array_splice($orderList, $k, 1);
    }
}
