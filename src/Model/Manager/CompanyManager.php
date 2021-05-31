<?php

namespace App\Model\Manager;

use App\Repository\CompanyRepository;
use App\Util\DTOExporter\DTOExporterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class CompanyManager extends AbstractCRUDManager implements CompanyManagerInterface
{

    /**
     * CompanyManager constructor.
     * @param EntityManagerInterface $entityManager
     * @param CompanyRepository $comapnyRepository
     * @param Security $security
     * @param DTOExporterInterface $companyDtoExporter
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        CompanyRepository $comapnyRepository,
        Security $security,
        DTOExporterInterface $companyDtoExporter,
    ) {
        parent::__construct($entityManager, $comapnyRepository, $security, $companyDtoExporter);
    }
}
