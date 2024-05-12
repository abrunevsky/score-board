<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Championship;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Championship>
 *
 * @method Championship|null find($id, $lockMode = null, $lockVersion = null)
 * @method Championship|null findOneBy(array $criteria, array $orderBy = null)
 * @method Championship[]    findAll()
 * @method Championship[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChampionshipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Championship::class);
    }

    public function findCurrent(): ?Championship
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
