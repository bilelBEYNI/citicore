<?php

namespace App\Repository;

use App\Entity\Demande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Demande>
 */
class DemandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Demande::class);
    }

    /**
     * Récupérer les demandes avec le statut "Acceptée"
     *
     * @return Demande[]
     */
    public function findAcceptedDemandes(): array
    {
        return $this->createQueryBuilder('d')
            ->where('d.statut = :statut')
            ->setParameter('statut', 'acceptée')
            ->getQuery()
            ->getResult();
    }
}
