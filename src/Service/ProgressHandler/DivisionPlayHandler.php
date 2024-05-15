<?php

declare(strict_types=1);

namespace App\Service\ProgressHandler;

use App\Entity\Championship;
use App\Entity\Play;
use App\Repository\PlayRepository;
use App\Service\PlayScoreProvider;
use Doctrine\ORM\EntityManagerInterface;

class DivisionPlayHandler implements ChampionshipHandlerInterface
{
    private PlayRepository $playRepository;
    private PlayScoreProvider $scoreProvider;
    private EntityManagerInterface $entityManager;

    public function __construct(
        PlayRepository $playRepository,
        PlayScoreProvider $scoreProvider,
        EntityManagerInterface $entityManager
    ) {
        $this->playRepository = $playRepository;
        $this->scoreProvider = $scoreProvider;
        $this->entityManager = $entityManager;
    }

    public function canProcess(Championship $championship): bool
    {
        return Championship::STATUS_PLAY === $championship->getStatus();
    }

    public function process(Championship $championship): void
    {
        [$current, $next] = $this->playRepository->findNextPair($championship);

        $this->entityManager->wrapInTransaction(function () use ($championship, $current, $next) {
            [$hostScore, $guestScore] = $this->scoreProvider->getScore($current->getHost(), $current->getGuest());
            /* @var Play $current */
            $current->setCompleted($hostScore, $guestScore);
            $championship->findPlayingTeamByTeam($current->getHost())->incrementScore($hostScore);
            $championship->findPlayingTeamByTeam($current->getGuest())->incrementScore($guestScore);

            if (!$next instanceof Play) {
                $championship->setStatus(Championship::STATUS_QUALIFYING);
            }
        });
    }

    public static function getIndex(): int
    {
        return 10;
    }
}
