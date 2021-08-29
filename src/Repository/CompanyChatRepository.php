<?php

namespace App\Repository;

use App\Entity\CompanyChat;
use App\Util\Factory\PropertyInfoExtractorFactory;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CompanyChat|null find($id, $lockMode = null, $lockVersion = null)
 * @method CompanyChat|null findOneBy(array $criteria, array $orderBy = null)
 * @method CompanyChat[]    findAll()
 * @method CompanyChat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyChatRepository extends EntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        PropertyInfoExtractorFactory $propertyInfoExtractorFactory = null
    ) {
        parent::__construct($registry, CompanyChat::class, $propertyInfoExtractorFactory);
    }
}
