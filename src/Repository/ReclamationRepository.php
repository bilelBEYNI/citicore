<?php

namespace App\Repository;

use App\Entity\Reclamation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

class ReclamationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reclamation::class);
    }


    /**
     * Récupère les réclamations pour un type donné.
     */
    public function findByType(string $type): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.Type_Reclamation = :type')
            ->setParameter('type', $type)
            ->orderBy('r.ID_Reclamation', 'ASC')
            ->getQuery()
            ->getResult();
    }


    /**
     * Récupère les réclamations filtrées, triées et recherchées.
     */
    public function findFilteredReclamations(?string $type, ?string $query, ?string $sortField = 'r.ID_Reclamation', ?string $sortDirection = 'ASC'): QueryBuilder
    {
        $qb = $this->createQueryBuilder('r');

        // Filtrage par type
        if ($type) {
            $qb->andWhere('r.Type_Reclamation = :type')
               ->setParameter('type', $type);
        }

        // Recherche par sujet
        if ($query) {
            $qb->andWhere('r.Sujet LIKE :q')
               ->setParameter('q', '%'.$query.'%');
        }

        // Tri dynamique
        $allowedSortFields = ['r.ID_Reclamation', 'r.Type_Reclamation', 'r.Sujet']; // sécuriser les champs de tri
        if (in_array($sortField, $allowedSortFields)) {
            $qb->orderBy($sortField, strtoupper($sortDirection) === 'DESC' ? 'DESC' : 'ASC');
        } else {
            $qb->orderBy('r.ID_Reclamation', 'ASC'); // tri par défaut
        }

        return $qb;
    }
}
