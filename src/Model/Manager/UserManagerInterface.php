<?php

namespace App\Model\Manager;

use App\Entity\User;
use App\Model\DTO\DTOInterface;
use App\Model\DTO\User\ChangePasswordDTO;
use App\Model\DTO\User\UserDTO;

/**
 * Interface UserManagerInterface.
 */
interface UserManagerInterface extends CRUDManagerInterface
{
    /**
     * @param string $username
     *
     * @return mixed
     */
    public function loadUserByUsername(string $username);

    /**
     * @param UserDTO $data
     *
     * @return User|array
     */
    public function googleAuthentication(DTOInterface $data);

    /**
     * @param string $emailFormData
     *
     * @return int
     *
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function processSendingPasswordResetEmail(string $emailFormData);

    /**
     * @param ChangePasswordDTO $changePasswordDTO
     */
    public function resetPassword(ChangePasswordDTO $changePasswordDTO);
}
