<?php

namespace App\Repository;

use App\Entity\PlaylistEpisode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlaylistEpisode>
 *
 * @method PlaylistEpisode|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlaylistEpisode|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlaylistEpisode[]    findAll()
 * @method PlaylistEpisode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaylistEpisodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlaylistEpisode::class);
    }

    public function save(PlaylistEpisode $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PlaylistEpisode $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return PlaylistEpisode[] Returns an array of PlaylistEpisode objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PlaylistEpisode
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
