<?php

declare(strict_types=1);

namespace App\Repository;

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
}
