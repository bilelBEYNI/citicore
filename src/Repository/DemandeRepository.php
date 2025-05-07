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
     * Récupérer le nombre de demandes par date (YYYY-MM-DD)
     *
     * @return array<int, array{date: string, count: int}>
     */
    public function countByDate(): array
    {
        return $this->createQueryBuilder('d')
            // ON EXTRAIT LES 10 PREMIERS CARACTÈRES (YYYY‑MM‑DD) DE LA DATE
            ->select("SUBSTRING(d.dateDemande, 1, 10) AS date, COUNT(d.demandeId) AS count")
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupérer les demandes acceptées
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

    /**
     * Compter les demandes par statut
     *
     * @return array<int, array{statut: string, count: int}>
     */
    public function countByStatut(): array
    {
        return $this->createQueryBuilder('d')
            ->select('d.statut AS statut, COUNT(d.demandeId) AS count')
            ->groupBy('d.statut')
            ->getQuery()
            ->getArrayResult();
    }
}
