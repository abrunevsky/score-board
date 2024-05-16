<?php

declare(strict_types=1);

namespace App\Service\ProgressHandler;

use App\Repository\PlayOffRepository;
use App\Service\PlayingTimeResolver;
use App\Service\PlayScoreProvider;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractPlayOfHandler implements ChampionshipHandlerInterface
{
    protected PlayOffRepository $playOffRepository;
    protected EntityManagerInterface $entityManager;
    protected PlayingTimeResolver $timeResolver;
    protected PlayScoreProvider $playScoreProvider;

    public function __construct(
        PlayOffRepository $playOffRepository,
        EntityManagerInterface $entityManager,
        PlayingTimeResolver $timeResolver,
        PlayScoreProvider $playScoreProvider
    ) {
        $this->playOffRepository = $playOffRepository;
        $this->entityManager = $entityManager;
        $this->timeResolver = $timeResolver;
        $this->playScoreProvider = $playScoreProvider;
    }
}
