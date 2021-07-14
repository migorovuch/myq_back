<?php

namespace App\Repository;

use App\Entity\Booking;
use App\Entity\Schedule;
use App\Model\DTO\AbstractFindDTO;
use App\Model\DTO\Booking\BookingFindDTO;
use App\Util\Factory\PropertyInfoExtractorFactory;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Booking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Booking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Booking[]    findAll()
 * @method Booking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry, PropertyInfoExtractorFactory $propertyInfoExtractorFactory)
    {
        parent::__construct($registry, Booking::class, $propertyInfoExtractorFactory);
    }

    /**
     * @param Criteria $criteria
     * @param BookingFindDTO $data
     * @return Criteria
     */
    protected function buildCriteriaByDTO(Criteria $criteria, AbstractFindDTO $data): Criteria
    {
        $criteria = parent::buildCriteriaByDTO($criteria, $data);
        if ($data->getFilterFrom()) {
            $criteria->andWhere($criteria->expr()->gt('end', $data->getFilterFrom()));
        }
        if ($data->getFilterTo()) {
            $criteria->andWhere($criteria->expr()->lt('start', $data->getFilterTo()));
        }

        return $criteria;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param BookingFindDTO $data
     * @return QueryBuilder
     */
    protected function buildQueryByDTO(QueryBuilder $queryBuilder, AbstractFindDTO $data): QueryBuilder
    {
        if ($data->getUserName() || $data->getUserPhone() || $data->getUser()) {
            $queryBuilder
                ->leftJoin('t.client', 'c');
            if ($data->getUser()) {
                $queryBuilder
                    ->andWhere($queryBuilder->expr()->eq('c.user', ':user'))
                    ->setParameter('user', $data->getUser());
            }
            if ($data->getUserName()) {
                $queryBuilder
                    ->andWhere('(t.userName LIKE :userName OR c.name LIKE :userName)')
                    ->setParameter('userName', $data->getUserName());
                $data->setUserName(null);
            }
            if ($data->getUserPhone()) {
                $queryBuilder
                    ->andWhere('(t.userPhone LIKE :userPhone OR c.phone LIKE :userPhone)')
                    ->setParameter('userPhone', $data->getUserPhone());
                $data->setUserPhone(null);
            }
        }

        $queryBuilder = parent::buildQueryByDTO($queryBuilder, $data);
        if ($data->getFilterFrom()) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->gt('t.end', ':filterFrom'))
                ->setParameter('filterFrom', $data->getFilterFrom());
        }
        if ($data->getFilterTo()) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->lt('t.start', ':filterTo'))
                ->setParameter('filterTo', $data->getFilterTo());
        }
        if ($data->getCompany()) {
            $queryBuilder
                ->innerJoin('t.schedule', 's')
                ->innerJoin('s.company', 'c')
                ->andWhere('c.id = :companyid')
                ->setParameter('companyid', $data->getCompany()->getId());
        }

        return $queryBuilder;
    }
}
