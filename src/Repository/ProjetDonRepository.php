<?php

namespace App\Repository;

use App\Entity\ProjetDon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProjetDon>
 */
class ProjetDonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjetDon::class);
    }

    //    /**
    //     * @return ProjetDon[] Returns an array of ProjetDon objects
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

    //    public function findOneBySomeField($value): ?ProjetDon
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    // Method to find projects by name (using LIKE for partial matching)
    public function findByName(string $name)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.nom LIKE :name')
            ->setParameter('name', '%' . $name . '%')
            ->getQuery()
            ->getResult();
    }
    // Method to find available projects (those whose end date is not in the past)
    public function findAvailableProjects()
    {
        return $this->createQueryBuilder('p')
            ->where('p.date_fin >= :now')
            ->setParameter('now', new \DateTime())  // Using the current date to check for availability
            ->getQuery()
            ->getResult();
    }

    // Method to find projects by name and availability (end date not passed)
    public function findByNameAndAvailable(string $name)
    {
        return $this->createQueryBuilder('p')
            ->where('p.nom LIKE :name')
            ->andWhere('p.date_fin >= :now')
            ->setParameter('name', '%' . $name . '%')
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getResult();
    }
    public function findBySearchQuery(string $searchQuery)
{
    $qb = $this->createQueryBuilder('p');

    if ($searchQuery) {
        $qb->where('p.nom LIKE :searchQuery OR p.description LIKE :searchQuery') // Assuming you search by name or description
            ->setParameter('searchQuery', '%' . $searchQuery . '%');
    }

    return $qb->getQuery()->getResult();
}


    
}
