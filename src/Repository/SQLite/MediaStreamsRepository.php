<?php

namespace App\Repository\SQLite;

use App\Entity\SQLite\MediaStreams;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MediaStreams|null find($id, $lockMode = null, $lockVersion = null)
 * @method MediaStreams|null findOneBy(array $criteria, array $orderBy = null)
 * @method MediaStreams[]    findAll()
 * @method MediaStreams[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MediaStreamsRepository extends ServiceEntityRepository
{
    /**
     * MediaStreamsRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MediaStreams::class);
    }
}
