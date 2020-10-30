<?php

namespace App\Repository\SQLite;

use App\Entity\SQLite\MediaItems;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MediaItems|null find($id, $lockMode = null, $lockVersion = null)
 * @method MediaItems|null findOneBy(array $criteria, array $orderBy = null)
 * @method MediaItems[]    findAll()
 * @method MediaItems[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MediaItemsRepository extends ServiceEntityRepository
{
    /**
     * MediaItemsRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MediaItems::class);
    }
}
