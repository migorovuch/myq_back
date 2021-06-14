<?php

namespace App\Model\Manager;

use App\Repository\BookingRepository;
use App\Util\DTOExporter\DTOExporterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class BookingManager extends AbstractCRUDManager implements BookingManagerInterface
{

    /**
     * BookingManager constructor.
     * @param EntityManagerInterface $entityManager
     * @param BookingRepository $bookingRepository
     * @param Security $security
     * @param DTOExporterInterface $bookingDtoExporter
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        BookingRepository $bookingRepository,
        Security $security,
        DTOExporterInterface $bookingDtoExporter,
    ) {
        parent::__construct($entityManager, $bookingRepository, $security, $bookingDtoExporter);
    }
}
