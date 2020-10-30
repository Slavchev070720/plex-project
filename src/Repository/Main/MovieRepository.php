<?php

namespace App\Repository\Main;

use App\Entity\Main\Movie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Movie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Movie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Movie[]    findAll()
 * @method Movie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MovieRepository extends ServiceEntityRepository
{
    /**
     * MovieRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Movie::class);
    }

    /**
     * @return Query
     */
    public function getAllMoviesQuery(): Query
    {
        $query = $this->createQueryBuilder('m')
            ->getQuery();
        return $query;
    }

    /**
     * @param string $search
     *
     * @return Query
     */
    public function getSearchMovieQuery(string $search): Query
    {
       return $this->createQueryBuilder('m')
            ->andWhere('m.title LIKE :title')
            ->setParameter('title', '%' . $search . '%')
            ->getQuery();
    }
}
