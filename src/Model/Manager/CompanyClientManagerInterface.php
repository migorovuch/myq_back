<?php

namespace App\Model\Manager;

use App\Entity\CompanyClient;
use App\Entity\User;
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
}
