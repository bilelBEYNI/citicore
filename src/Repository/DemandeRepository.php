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

    public function countByStatut(): array
    {
        $qb = $this->createQueryBuilder('d')
            ->select('d.statut, COUNT(d.demandeId) AS count')  // Correction du nom de la colonne
            ->groupBy('d.statut')
            ->getQuery();

        $result = $qb->getResult();

        // Transformer les résultats en un tableau associatif pour une utilisation facile
        $countByStatut = [
            'Acceptée' => 0,
            'En attente' => 0,
            'Refusée' => 0,
        ];

        foreach ($result as $row) {
            $countByStatut[$row['statut']] = (int) $row['count'];
        }

        return $countByStatut;
    }
    
}
