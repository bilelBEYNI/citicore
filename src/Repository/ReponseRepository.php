<?php

namespace App\Repository;

use App\Entity\Reponse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
    
}
