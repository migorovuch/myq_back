<?php

namespace App\Repository;

use App\Entity\Booking;
use App\Model\DTO\AbstractFindDTO;
use App\Model\DTO\Booking\BookingFindDTO;
use App\Util\Factory\PropertyInfoExtractorFactory;
use Doctrine\Common\Collections\Criteria;
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
     * @param Criteria       $criteria
     * @param BookingFindDTO $data
     *
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
     * @param QueryBuilder   $queryBuilder
     * @param BookingFindDTO $data
     *
     * @return QueryBuilder
     */
    protected function buildQueryByDTO(QueryBuilder $queryBuilder, AbstractFindDTO $data): QueryBuilder
    {
        $queryBuilder = parent::buildQueryByDTO($queryBuilder, $data);
        $queryBuilder
            ->innerJoin('t.schedule', 's')
            ->innerJoin('s.company', 'c');
        if ($data->getUserName() || $data->getUserPhone() || $data->getUser()) {
            $queryBuilder
                ->leftJoin('t.client', 'cl');
            if ($data->getUser()) {
                $queryBuilder
                    ->andWhere($queryBuilder->expr()->eq('cl.user', ':user'))
                    ->setParameter('user', $data->getUser());
            }
            if ($data->getUserName()) {
                $queryBuilder
                    ->andWhere('cl.name LIKE :userName')
                    ->setParameter('userName', $data->getUserName());
            }
            if ($data->getUserPhone()) {
                $queryBuilder
                    ->andWhere('cl.phone LIKE :userPhone')
                    ->setParameter('userPhone', $data->getUserPhone());
            }
        }
        if ($data->getCompanyName()) {
            $queryBuilder
                ->andWhere('c.name LIKE :companyName')
                ->setParameter('companyName', $data->getCompanyName());
        }
        if ($data->getScheduleName()) {
            $queryBuilder
                ->andWhere('s.name LIKE :scheduleName')
                ->setParameter('scheduleName', $data->getScheduleName());
        }
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
                ->andWhere('c.id = :companyid')
                ->setParameter('companyid', $data->getCompany()->getId());
        }

        return $queryBuilder;
    }

    /**
     * @param string $companyId
     * @param string $bookingId
     *
     * @return Booking
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findCompanyBooking(string $companyId, string $bookingId)
    {
        return $this->createQueryBuilder('t')
            ->innerJoin('t.schedule', 's')
            ->andWhere('t.id = :id')
            ->setParameter('id', $bookingId)
            ->andWhere('s.company = :company')
            ->setParameter('company', $companyId)
            ->getQuery()
            ->getSingleResult();
    }
}
