<?php

namespace App\Repository;

use App\Entity\Booking;
use App\Model\DTO\AbstractFindDTO;
use App\Model\DTO\Booking\BookingFindDTO;
use App\Util\Factory\PropertyInfoExtractorFactory;
use Doctrine\Common\Collections\Criteria;
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
    public function buildCriteriaByDTO(Criteria $criteria, AbstractFindDTO $data): Criteria
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
}
