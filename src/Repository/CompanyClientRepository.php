<?php

namespace App\Repository;

use App\Entity\CompanyClient;
use App\Util\Factory\PropertyInfoExtractorFactory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CompanyClient|null find($id, $lockMode = null, $lockVersion = null)
 * @method CompanyClient|null findOneBy(array $criteria, array $orderBy = null)
 * @method CompanyClient[]    findAll()
 * @method CompanyClient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyClientRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry, PropertyInfoExtractorFactory $propertyInfoExtractorFactory = null)
    {
        parent::__construct($registry, CompanyClient::class, $propertyInfoExtractorFactory);
    }
}
