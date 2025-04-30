<?php

namespace App\Repository;

use App\Entity\Evenement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<Evenement>
 */
class EvenementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evenement::class);
    }

    /**
     * Récupère les événements filtrés, triés et recherchés.
     */
    public function findFilteredEvenements(?string $lieu, ?string $query, ?string $sortField = 'e.id_evenement', ?string $sortDirection = 'ASC'): QueryBuilder
    {
        $qb = $this->createQueryBuilder('e');

        // Filtrage par lieu
        if ($lieu) {
            $qb->andWhere('e.lieu_evenement = :lieu')
               ->setParameter('lieu', $lieu);
        }

        // Recherche par nom d'événement
        if ($query) {
            $qb->andWhere('e.nom_evenement LIKE :q')
               ->setParameter('q', '%' . $query . '%');
        }

        // Tri dynamique
        $allowedSortFields = ['e.id_evenement', 'e.nom_evenement', 'e.date_evenement', 'e.lieu_evenement']; // sécuriser les champs de tri
        if (in_array($sortField, $allowedSortFields)) {
            $qb->orderBy($sortField, strtoupper($sortDirection) === 'DESC' ? 'DESC' : 'ASC');
        } else {
            $qb->orderBy('e.id_evenement', 'ASC'); // tri par défaut
        }

        return $qb;
    }

    //    /**
    //     * @return Evenement[] Returns an array of Evenement objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Evenement
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
