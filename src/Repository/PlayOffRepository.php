<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\AbstractPlay;
use App\Entity\Championship;
use App\Entity\PlayOff;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlayOff>
 *
 * @method PlayOff|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlayOff|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlayOff[]    findAll()
 * @method PlayOff[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayOffRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlayOff::class);
    }

    /**
     * @return array{0: PlayOff, 1?: PlayOff}
     */
    public function findNextPair(Championship $championship, string $stage): array
    {
        return array_pad(
            $this->createQueryBuilder('p')
                ->join('p.team1', 't1')
                ->join('p.team2', 't2')
                ->addSelect(['t1', 't2'])
                ->where('p.championship = :championship')
                ->andWhere('p.status = :pendingStatus')
                ->andWhere('p.stage = :stage')
                ->setParameter('championship', $championship)
                ->setParameter('pendingStatus', AbstractPlay::STATUS_PENDING)
                ->setParameter('stage', $stage)
                ->orderBy('p.playAt', 'ASC')
                ->setMaxResults(2)
                ->getQuery()
                ->getResult(),
            2,
            null
        );
    }
}
