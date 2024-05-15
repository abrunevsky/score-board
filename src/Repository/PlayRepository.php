<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Championship;
use App\Entity\Play;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Play>
 *
 * @method Play|null find($id, $lockMode = null, $lockVersion = null)
 * @method Play|null findOneBy(array $criteria, array $orderBy = null)
 * @method Play[]    findAll()
 * @method Play[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Play::class);
    }

    /**
     * @return array{0: Play, 1?: Play}
     */
    public function findNextPair(Championship $championship): array
    {
        return array_pad(
            $this->createQueryBuilder('p')
                ->join('p.host', 'ht')
                ->join('p.guest', 'gt')
                ->addSelect(['ht', 'gt'])
                ->where('p.championship = :championship')
                ->andWhere('p.status = :pendingStatus')
                ->setParameter('championship', $championship)
                ->setParameter('pendingStatus', Play::STATUS_PENDING)
                ->orderBy('p.playAt', 'ASC')
                ->setMaxResults(2)
                ->getQuery()
                ->getResult(),
            2,
            null
        );
    }
}
