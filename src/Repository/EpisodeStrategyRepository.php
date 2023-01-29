<?php

namespace App\Repository;

use App\Entity\EpisodeStrategy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EpisodeStrategy>
 *
 * @method EpisodeStrategy|null find($id, $lockMode = null, $lockVersion = null)
 * @method EpisodeStrategy|null findOneBy(array $criteria, array $orderBy = null)
 * @method EpisodeStrategy[]    findAll()
 * @method EpisodeStrategy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EpisodeStrategyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EpisodeStrategy::class);
    }

    public function save(EpisodeStrategy $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(EpisodeStrategy $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
