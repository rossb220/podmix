<?php

namespace App\Repository;

use App\Entity\PlaylistConfig;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlaylistConfig>
 *
 * @method PlaylistConfig|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlaylistConfig|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlaylistConfig[]    findAll()
 * @method PlaylistConfig[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaylistConfigRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlaylistConfig::class);
    }

    public function save(PlaylistConfig $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PlaylistConfig $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
