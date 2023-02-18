<?php

namespace App\Repository;

use App\Entity\PlaylistEpisode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;

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

    public function removeAllByPlaylistId(string $playlistId)
    {
        $this->createQueryBuilder('pe')
            ->delete(PlaylistEpisode::class, 'pe')
            ->where('pe.playlist = :playlistId')
            ->setParameter('playlistId', $playlistId, UuidType::NAME)
            ->getQuery()
            ->getResult();
    }
}