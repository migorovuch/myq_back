<?php

namespace App\Model\Manager;

use App\Entity\CompanyClient;
use App\Model\DTO\CompanyClient\CompanyClientDTO;
use App\Repository\CompanyClientRepository;
use App\Util\DTOExporter\DTOExporterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class CompanyClientManager extends AbstractCRUDManager implements CompanyClientManagerInterface
{

    /**
     * CompanyManager constructor.
     * @param EntityManagerInterface $entityManager
     * @param CompanyClientRepository $comapnyClientRepository
     * @param Security $security
     * @param DTOExporterInterface $companyDtoExporter
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        CompanyClientRepository $comapnyClientRepository,
        Security $security,
        DTOExporterInterface $companyDtoExporter,
    ) {
        parent::__construct($entityManager, $comapnyClientRepository, $security, $companyDtoExporter);
    }

    /**
     * @inheritDoc
     */
    public function getByDTO(CompanyClientDTO $companyClientDTO): CompanyClient
    {
        $companyClient = null;
        if ($companyClientDTO->getUser()) {
            $companyClient = $this->entityRepository->findOneBy(
                [
                    'user' => $companyClientDTO->getUser(),
                    'company' => $companyClientDTO->getCompany()
                ]);
            if (
                !$companyClient &&
                $companyClientDTO->getClient() &&
                !$companyClientDTO->getClient()->getUser()
            ) {
                $companyClient = $companyClientDTO->getClient();
                $companyClient->setUser($companyClientDTO->getUser());
                $this->save($companyClient);
            }
        } elseif ($companyClientDTO->getClient() && !$companyClientDTO->getClient()->getUser()) {
            $companyClient = $companyClientDTO->getClient();
        }
        if (!$companyClient) {
            $companyClient = $this->create($companyClientDTO);
        }

        return $companyClient;
    }
}
