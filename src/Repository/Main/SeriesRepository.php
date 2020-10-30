<?php

namespace App\Repository\Main;

use App\Entity\Main\Series;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\Query;

/**
 * @method Series|null find($id, $lockMode = null, $lockVersion = null)
 * @method Series|null findOneBy(array $criteria, array $orderBy = null)
 * @method Series[]    findAll()
 * @method Series[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SeriesRepository extends ServiceEntityRepository
{
    /**
     * SeriesRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Series::class);
    }

    /**
     * @return Query
     */
    public function getAllSeriesQuery(): Query
    {
        $query = $this->createQueryBuilder('m')
            ->getQuery();
        return $query;
    }

    /**
     * @param string $title
     *
     * @return Query
     */
    public function getSeriesQueryByTitle(string $title): Query
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.title LIKE :title')
            ->setParameter('title', '%' . $title . '%')
            ->getQuery();
    }
}
