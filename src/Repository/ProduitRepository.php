<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produit>
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    //    /**
    //     * @return Produit[] Returns an array of Produit objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Produit
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }


public function findProduitsParVenteAsc(): array
{
    $entityManager = $this->getEntityManager();

    $query = $entityManager->createQuery(
        'SELECT p, COALESCE(SUM(cp.quantite), 0) AS quantiteVendue
         FROM App\Entity\Produit p
         LEFT JOIN App\Entity\CommandeProduit cp WITH cp.produit = p
         GROUP BY p.id_produit
         ORDER BY quantiteVendue ASC'
    );

    return $query->getResult();
}

public function findLeastSoldProduct(): ?Produit
{
    return $this->createQueryBuilder('p')
        ->leftJoin('p.commandeProduits', 'cp')
        ->groupBy('p.id')
        ->orderBy('SUM(cp.quantite)', 'ASC')
        ->setMaxResults(1)
        ->getQuery()
        ->getOneOrNullResult();
}


public function ventesParProduit(ProduitRepository $produitRepository): Response
{
    $produits = $this->getDoctrine()->getRepository(Produit::class)->createQueryBuilder('p')
        ->select('p.nom, SUM(cp.quantite) AS totalVendus')
        ->leftJoin('p.commandeProduits', 'cp')
        ->groupBy('p.id')
        ->getQuery()
        ->getResult();

    return $this->render('admin/stats.html.twig', [
        'produits' => $produits,
    ]);
}

}