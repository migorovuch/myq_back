<?php

namespace App\Model\Manager;

use App\Entity\CompanyClient;
use App\Entity\User;
use App\Exception\AccessDeniedException;
use App\Exception\ClientHasNoUserRelationException;
use App\Exception\TryingToUseExistingAccountException;
use App\Model\DTO\CompanyClient\CompanyClientDTO;
use App\Repository\BookingRepository;
use App\Repository\CompanyClientRepository;
use App\Util\DTOExporter\DTOExporterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class CompanyClientManager extends AbstractCRUDManager implements CompanyClientManagerInterface
{
    /**
     * CompanyManager constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param CompanyClientRepository $comapnyClientRepository
     * @param Security $security
     * @param DTOExporterInterface $companyDtoExporter
     * @param BookingRepository $bookingRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        CompanyClientRepository $comapnyClientRepository,
        Security $security,
        DTOExporterInterface $companyDtoExporter,
        protected BookingRepository $bookingRepository
    ) {
        parent::__construct($entityManager, $comapnyClientRepository, $security, $companyDtoExporter);
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function checkExistingClientForBooking(CompanyClient $existingClient, ?UserInterface $currentUser): ?CompanyClient
    {
        if ($existingClient->getUser() && !$currentUser) {
            throw new TryingToUseExistingAccountException();
        }
        if ($existingClient->getUser() && $currentUser && $existingClient->getUser()->getId() !== $currentUser->getId()) {
            throw new AccessDeniedException();
        }
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
     * {@inheritDoc}
     */
    public function changeClientDetails(User $user)
    {
        return $this->entityRepository->changeClientDetails($user);
    }

    /**
     * @param array $clientsIDs
     * @return CompanyClient[]
     */
    public function updateUserClientsList(array $clientsIDs): array
    {
        $currentUser = $this->security->getUser();
        /** @var CompanyClient[] $companyClients */
        $companyClients = $this->entityRepository->getListByIDsWithoutUser($clientsIDs);
        $newClients = [];
        foreach ($companyClients as $companyClient) {
            $companyClient->setUser($currentUser);
            $this->entityManager->persist($companyClient);
            $newClients[] = $companyClient;
        }
        $this->entityManager->flush();
        foreach ($newClients as $newClient) {
            $this->mergeUserCompanyClients($newClient);
        }

        return $companyClients;
    }

    /**
     * @param CompanyClient $companyClient
     */
    public function mergeUserCompanyClients(CompanyClient $companyClient)
    {
        if (!$companyClient->getUser()) {
            throw new ClientHasNoUserRelationException();
        }
        $userCompanyClients = $this->entityRepository->findBy([
            'user' => $companyClient->getUser(),
            'company' => $companyClient->getCompany(),
        ]);
        /** @var CompanyClient $userCompanyClient */
        foreach ($userCompanyClients as $userCompanyClient) {
            if (
                $userCompanyClient->getUser()->getId() === $companyClient->getUser()->getId() &&
                $userCompanyClient->getCompany()->getId() === $companyClient->getCompany()->getId() &&
                $userCompanyClient->getId() !== $companyClient->getId()
            ) {
                $this->bookingRepository->changeBookingsClient($userCompanyClient, $companyClient);
                $userCompanyClient->setDeleted(CompanyClient::STATE_DELETED);
                $this->save($userCompanyClient);
            }
        }
    }
}
