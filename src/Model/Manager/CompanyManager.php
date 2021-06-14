<?php

namespace App\Model\Manager;

use App\Entity\Company;
use App\Model\DTO\Company\CompanyFindDTO;
use App\Repository\CompanyRepository;
use App\Util\DTOExporter\DTOExporterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
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

    /**
     * @param CompanyFindDTO $data
     * @return array
     */
    public function findPublicByDTO(CompanyFindDTO $data): array
    {
        if (!(
            $this->security->isGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY) &&
            $data->getUser() &&
            $this->security->getUser()->getId() == $data->getUser()->getId()
        )) {
            $data = new CompanyFindDTO(
                $data->getId(),
                $data->getName(),
                $data->getEmail(),
                $data->getPhone(),
                $data->getAddress(),
                Company::STATUS_ON,
                $data->getUser(),
                $data->getSort(),
                $data->getPage(),
                $data->getCondition()
            );
        }

        return $this->findByDTO($data);
    }
}
