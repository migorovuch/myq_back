<?php

namespace App\Repository;

use App\Entity\SpecialHours;
use App\Model\DTO\AbstractFindDTO;
use App\Model\DTO\SpecialHours\SpecialHoursFindDTO;
use App\Util\Factory\PropertyInfoExtractorFactory;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SpecialHours|null find($id, $lockMode = null, $lockVersion = null)
 * @method SpecialHours|null findOneBy(array $criteria, array $orderBy = null)
 * @method SpecialHours[]    findAll()
 * @method SpecialHours[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpecialHoursRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry, PropertyInfoExtractorFactory $propertyInfoExtractorFactory)
    {
        parent::__construct($registry, SpecialHours::class, $propertyInfoExtractorFactory);
    }

    /**
     * @param Criteria $criteria
     * @param SpecialHoursFindDTO $data
     * @return Criteria
     */
    public function buildCriteriaByDTO(Criteria $criteria, AbstractFindDTO $data): Criteria
    {
        $criteria = parent::buildCriteriaByDTO($criteria, $data);
        if ($data->getFilterFrom()) {
            $criteria->andWhere($criteria->expr()->gte('endDate', $data->getFilterFrom()));
        }
        if ($data->getFilterTo()) {
            $criteria->andWhere($criteria->expr()->lte('startDate', $data->getFilterTo()));
        }

        return $criteria;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param SpecialHoursFindDTO $data
     * @return QueryBuilder
     */
    protected function buildQueryByDTO(QueryBuilder $queryBuilder, AbstractFindDTO $data): QueryBuilder
    {
        $queryBuilder = parent::buildQueryByDTO($queryBuilder, $data);
        if ($data->getFilterFrom()) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->gte('t.endDate', ':filterFrom'))
                ->setParameter('filterFrom', $data->getFilterFrom());
        }
        if ($data->getFilterTo()) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->lte('t.startDate', ':filterTo'))
                ->setParameter('filterTo', $data->getFilterTo());
        }

        return $queryBuilder;
    }
}
