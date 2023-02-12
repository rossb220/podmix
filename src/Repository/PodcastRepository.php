<?php

namespace App\Repository;

use App\Entity\Podcast;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;

/**
 * @extends ServiceEntityRepository<Podcast>
 *
 * @method Podcast|null find($id, $lockMode = null, $lockVersion = null)
 * @method Podcast|null findOneBy(array $criteria, array $orderBy = null)
 * @method Podcast[]    findAll()
 * @method Podcast[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PodcastRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Podcast::class);
    }

    public function save(Podcast $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Podcast $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getOneWithEpisodes(string $podcastId): Podcast
    {
        return $this->createQueryBuilder('p')
            ->where('p.id = :podcastId')
            ->andWhere('p.disabledAt is NULL')
            ->setParameter('podcastId', $podcastId, UuidType::NAME)
            ->leftJoin('p.episodes', 'e')
            ->getQuery()
            ->getSingleResult();
    }
    public function getOne(string $podcastId): Podcast
    {
        return $this->createQueryBuilder('p')
            ->where('p.id = :podcastId')
            ->andWhere('p.disabledAt is NULL')
            ->leftJoin('p.episodes', 'e')
            ->setParameter('podcastId', $podcastId, UuidType::NAME)
            ->getQuery()
            ->getSingleResult();
    }
    public function getAll(): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.disabledAt is NULL')
            ->leftJoin('p.episodes', 'e')
            ->getQuery()
            ->getResult();
    }
}
