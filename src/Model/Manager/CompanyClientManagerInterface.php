<?php

namespace App\Model\Manager;

use App\Entity\CompanyClient;
use App\Entity\User;
use App\Model\DTO\User\ChangeUserClientsListDTO;
use Symfony\Component\Security\Core\User\UserInterface;

interface CompanyClientManagerInterface extends CRUDManagerInterface
{
    /**
     * @param CompanyClient      $existingClient
     * @param UserInterface|null $currentUser
     *
     * @return CompanyClient|null
     */
    public function checkExistingClientForBooking(CompanyClient $existingClient, ?UserInterface $currentUser): ?CompanyClient;

    /**
     * @param User $user
     */
    public function changeClientDetails(User $user);

    /**
     * @param array $clientsIDs
     * @return array
     */
    public function updateUserClientsList(array $clientsIDs): array;
}
