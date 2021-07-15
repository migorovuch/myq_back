<?php

namespace App\Repository;

use App\Entity\CompanyClient;
use App\Model\DTO\AbstractFindDTO;
use App\Util\Factory\PropertyInfoExtractorFactory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CompanyClient|null find($id, $lockMode = null, $lockVersion = null)
 * @method CompanyClient|null findOneBy(array $criteria, array $orderBy = null)
 * @method CompanyClient[]    findAll()
 * @method CompanyClient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyClientRepository extends EntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        PropertyInfoExtractorFactory $propertyInfoExtractorFactory = null
    ) {
        parent::__construct($registry, CompanyClient::class, $propertyInfoExtractorFactory);
    }

    public function findByDTO(AbstractFindDTO $data)
    {
        $qb = $this->createQueryBuilder('t');
        $qb = $this->buildQueryByDTO($qb, $data)
            ->leftJoin('t.bookings', 'b')
            ->addSelect('COUNT(b.id) as numberOfBookings')
            ->groupBy('t.id');
        $qb = $this->paginationQueryByDTO($qb, $data);
        $query = $qb->getQuery();

        $searchResult = $query->getResult();
        $clients = [];
        foreach ($searchResult as $companyClient) {
            $clients[] = ($companyClient[0])->setNumberOfBookings($companyClient['numberOfBookings']);
        }

        return $clients;
    }
}
