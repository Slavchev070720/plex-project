<?php

namespace App\Repository\SQLite;

use App\Entity\SQLite\MetadataItems;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MetadataItems|null find($id, $lockMode = null, $lockVersion = null)
 * @method MetadataItems|null findOneBy(array $criteria, array $orderBy = null)
 * @method MetadataItems[]    findAll()
 * @method MetadataItems[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MetadataItemsRepository extends ServiceEntityRepository
{
    /**
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MetadataItems::class);
    }

    /**
     * @param integer $limit
     * @param integer $offset
     *
     * @return MetadataItems[] Returns an array of MetadataItems objects
     */
    public function findAllMovies($limit = null, $offset = null)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.metadataType = 1')
            ->andWhere('m.guid LIKE :guid OR m.guid LIKE :guid2')
            ->setParameter('guid', '%themoviedb://%')
            ->setParameter('guid2', '%imdb://%')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param integer $limit
     * @param integer $offset
     *
     * @return MetadataItems[] Returns an array of MetadataItems objects
     */
    public function findAllSeries($limit = null, $offset = null)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.metadataType = 2')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }
}
