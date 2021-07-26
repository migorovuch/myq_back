<?php

namespace App\Model\Manager;

use App\Entity\CompanyClient;
use App\Entity\User;
use App\Exception\AccessDeniedException;
use App\Model\DTO\AbstractFindDTO;
use App\Model\DTO\CompanyClient\CompanyClientDTO;
use App\Repository\CompanyClientRepository;
use App\Util\DTOExporter\DTOExporterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

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
    public function getByDTO(CompanyClientDTO $companyClientDTO, ?CompanyClient $existingClient): CompanyClient
    {
        $companyClient = null;
        // getUser - current authenticated user
        if ($companyClientDTO->getUser()) {
            // use current user client
            $companyClient = $this->findOneBy(
                [
                    'user' => $companyClientDTO->getUser(),
                    'company' => $companyClientDTO->getCompany(),
                ]);
            // if no client was found, check existingClient
            if (
                !$companyClient &&
                $existingClient &&
                !$existingClient->getUser() &&
                $existingClient->getCompany()->getId() === $companyClientDTO->getCompany()->getId()
            ) {
                $companyClient = $existingClient;
                $companyClient->setUser($companyClientDTO->getUser());
                $this->save($companyClient);
            }
        } elseif ($existingClient && !$existingClient->getUser()) {
            $companyClient = $existingClient;
        } elseif ($existingClient && $existingClient->getUser()) {
            throw new AccessDeniedException();
        }
        if (!$companyClient) {
            $companyClient = $this->create($companyClientDTO);
        }

        return $companyClient;
    }

    /**
     * @inheritDoc
     */
    public function checkExistingClientForBooking(CompanyClient $existingClient, ?UserInterface $currentUser): ?CompanyClient
    {
//        if (
//            ($existingClient->getUser() && !$currentUser) ||
//            ($existingClient->getUser() && $currentUser && $existingClient->getUser()->getId() !== $currentUser->getId())
//        ) {
//            throw new AccessDeniedException();
//        }
        if (
            ($existingClient->getUser() && $currentUser && $existingClient->getUser()->getId() === $currentUser->getId()) ||
            (!$existingClient->getUser() && !$currentUser)
        ) {
            return $existingClient;
        } elseif (!$existingClient->getUser() && $currentUser) {
            $existingClient->setUser($currentUser);
            $this->save($existingClient);

            return $existingClient;
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function changeClientDetails(User $user)
    {
        return $this->entityRepository->changeClientDetails($user);
    }
}
