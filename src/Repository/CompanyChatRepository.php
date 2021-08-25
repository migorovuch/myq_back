<?php

namespace App\Repository;

use App\Entity\CompanyChat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CompanyChat|null find($id, $lockMode = null, $lockVersion = null)
 * @method CompanyChat|null findOneBy(array $criteria, array $orderBy = null)
 * @method CompanyChat[]    findAll()
 * @method CompanyChat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyChatRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompanyChat::class);
    }
}
