<?php

declare(strict_types=1);

namespace App\Service\ProgressHandler;

use App\Entity\Championship;
use Doctrine\ORM\EntityManagerInterface;

class PlayOfQualifyingHandler implements ChampionshipHandlerInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function canProcess(Championship $championship): bool
    {
        return Championship::STATUS_QUALIFYING === $championship->getStatus();
    }

    public function process(Championship $championship): void
    {
        // @todo: temp solution for testing
        $championship->setStatus(Championship::STATUS_FINISHED);
        $this->entityManager->flush();
    }

    public static function getIndex(): int
    {
        return 20;
    }
}
