<?php

namespace App\Repository;

use App\Entity\Episode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<Episode>
 *
 * @method Episode|null find($id, $lockMode = null, $lockVersion = null)
 * @method Episode|null findOneBy(array $criteria, array $orderBy = null)
 * @method Episode[]    findAll()
 * @method Episode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EpisodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Episode::class);
    }

    public function save(Episode $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Episode $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getAllByPodcastIds(array $podcastIds): array
    {
        $convertedToDbValue = array_map(function (string $id) {
            return Uuid::fromString($id)->toBinary();
        }, $podcastIds);

        return $this->createQueryBuilder('e')
            ->where('p.id in (:podcastIds)')
            ->andWhere('p.disabledAt is NULL')
            ->join('e.podcast', 'p')
            ->setParameter('podcastIds', $convertedToDbValue)
            ->getQuery()
            ->getResult();
    }

    public function getAll(): array
    {
        return $this->createQueryBuilder('e')
            ->where('p.disabledAt is NULL')
            ->join('e.podcast', 'p')
            ->getQuery()
            ->getResult();
    }
}
