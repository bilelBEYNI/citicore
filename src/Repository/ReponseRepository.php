<?php

namespace App\Repository;

use App\Entity\Reponse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<Reponse>
 */
class ReponseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reponse::class);
    }

    public function countByType(): array
    {
        return $this->createQueryBuilder('r')
            ->select('rec.Type_Reclamation AS type, COUNT(r.ID_Reponse) AS count')
            ->join('r.reclamation', 'rec')
            ->groupBy('rec.Type_Reclamation')
            ->getQuery()
            ->getResult();
    }

    public function countByStatus(): array
    {
        return $this->createQueryBuilder('r')
            ->select('r.Statut AS statut, COUNT(r.ID_Reponse) AS count')
            ->groupBy('r.Statut')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère les réponses filtrées, recherchées et triées.
     */
    public function findFilteredReponses(?int $recId, ?string $statut, ?string $query, ?string $sortField = 'r.DateReponse', ?string $sortDirection = 'DESC'): QueryBuilder
    {
        $qb = $this->createQueryBuilder('r')
            ->join('r.reclamation', 'rec')
            ->addSelect('rec');

        // Filtrage par réclamation
        if ($recId) {
            $qb->andWhere('rec.ID_Reclamation = :recId')
               ->setParameter('recId', $recId);
        }

        // Filtrage par statut
        if ($statut) {
            $qb->andWhere('r.Statut = :statut')
               ->setParameter('statut', $statut);
        }

        // Recherche dans contenu ou sujet
        if ($query) {
            $qb->andWhere('r.Contenu LIKE :q OR rec.Sujet LIKE :q')
               ->setParameter('q', '%'.$query.'%');
        }

        // Tri dynamique sécurisé
        $allowedSortFields = ['r.DateReponse', 'r.Statut', 'rec.Sujet']; 
        if (in_array($sortField, $allowedSortFields)) {
            $qb->orderBy($sortField, strtoupper($sortDirection) === 'DESC' ? 'DESC' : 'ASC');
        } else {
            $qb->orderBy('r.DateReponse', 'DESC'); // tri par défaut
        }

        return $qb;
    }
}
