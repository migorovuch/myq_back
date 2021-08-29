<?php

namespace App\Model\Manager;

use App\Entity\User;
use App\Model\DTO\DTOInterface;
use App\Model\DTO\User\ApproveEmailDTO;
use App\Model\DTO\User\ChangeAccountDTO;
use App\Model\DTO\User\ChangePasswordDTO;
use App\Model\DTO\User\RegistrationDTO;
use App\Model\Model\EntityInterface;

/**
 * Interface UserManagerInterface.
 */
interface UserManagerInterface extends CRUDManagerInterface
{
    const EMPTY_NICKNAME = '-';

    /**
     * @param string $username
     *
     * @return mixed
     */
    public function loadUserByUsername(string $username);

    /**
     * @param RegistrationDTO $data
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

    /**
     * @param DTOInterface $data
     *
     * @return EntityInterface
     */
    public function registration(DTOInterface $data): EntityInterface;

    /**
     * @param ApproveEmailDTO $approveEmailDTO
     *
     * @return User|null
     */
    public function approveEmail(ApproveEmailDTO $approveEmailDTO): ?User;

    /**
     * @param ChangeAccountDTO $data
     *
     * @return EntityInterface
     */
    public function changeAccount(ChangeAccountDTO $data): EntityInterface;

    /**
     * @param string      $email
     * @param string|null $exceptId
     *
     * @return bool
     */
    public function ifEmailExists(string $email, string $exceptId = null);

    /**
     * @param string      $nickname
     * @param string|null $exceptId
     *
     * @return mixed
     */
    public function ifNicknameExists(string $nickname, string $exceptId = null);
}
