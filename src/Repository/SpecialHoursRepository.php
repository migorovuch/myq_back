<?php

namespace App\Repository;

use App\Entity\SpecialHours;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SpecialHours|null find($id, $lockMode = null, $lockVersion = null)
 * @method SpecialHours|null findOneBy(array $criteria, array $orderBy = null)
 * @method SpecialHours[]    findAll()
 * @method SpecialHours[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpecialHoursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SpecialHours::class);
    }

    // /**
    //  * @return SpecialHours[] Returns an array of SpecialHours objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SpecialHours
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
