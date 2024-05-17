<?php

declare(strict_types=1);

namespace App\Service\ProgressHandler;

use App\Entity\Championship;
use App\Entity\PlayOff;
use App\Entity\Team;
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

    public function process(Championship $championship): void
    {
        $this->entityManager->wrapInTransaction(function () use ($championship) {
            [$current, $next] = $this->playOffRepository->findNextPair($championship, static::getCurrentStage());
            /* @var PlayOff $current */
            [$team1Score, $team2Score] = $this->playScoreProvider->getScore($current->getTeam1(), $current->getTeam2());
            $current->setCompleted($team1Score, $team2Score);
            $championship->findPlayingTeamByTeam($current->getTeam1())->incrementScore($team1Score);
            $championship->findPlayingTeamByTeam($current->getTeam2())->incrementScore($team2Score);

            if (!$next instanceof PlayOff) {
                if ($this instanceof NextStageAwareInterface) {
                    $this->prepareNextStage($championship);
                }
                $championship->setStatus(static::getNextChampionshipStatus());
            }
        });
    }

    /**
     * @return PlayOff[]
     */
    protected function getCurrentPlays(Championship $championship, int $expectedPlaysQty): array
    {
        $currentPlays = array_filter(
            $championship->getPlayOffs(),
            static function (PlayOff $playOff) {
                return $playOff->isCompleted() && static::getCurrentStage() === $playOff->getStage();
            },
        );

        if ($expectedPlaysQty !== count($currentPlays)) {
            throw new \RuntimeException(sprintf('Unexpected qty of plays: %d has been found, but %s was expected', count($currentPlays), $expectedPlaysQty));
        }

        return $currentPlays;
    }

    protected static function getWinner(PlayOff $play): Team
    {
        return ($play->getTeam1Score() > $play->getTeam2Score()) ? $play->getTeam1() : $play->getTeam2();
    }

    abstract protected static function getNextChampionshipStatus(): string;

    abstract protected static function getCurrentStage(): string;
}
