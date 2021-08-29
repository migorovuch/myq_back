<?php

namespace App\Security;

use App\Exception\ApiException;
use App\Model\Model\EntityInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class AbstractVoter extends Voter implements VoterInterface
{
    const CREATE = 'create';
    const VIEW = 'view';
    const UPDATE = 'update';
    const DELETE = 'delete';

    /**
     * @param array          $attributes
     * @param mixed          $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attributes, $subject, TokenInterface $token)
    {
        $currentUser = $token->getUser();
        if ('anon.' !== $currentUser) {
            switch ($attributes) {
                case static::CREATE:
                    return $this->canCreate($currentUser, $subject);
                case static::VIEW:
                    return $this->canView($currentUser, $subject);
                case static::UPDATE:
                    return $this->canEdit($currentUser, $subject);
                case static::DELETE:
                    return $this->canDelete($currentUser, $subject);
            }
            throw new ApiException('This code should not be reached!');
        }

        return false;
    }

    /**
     * @param UserInterface|string $currentUser
     * @param EntityInterface      $subject
     *
     * @return bool
     */
    abstract protected function canCreate(UserInterface | string $currentUser, EntityInterface $subject): bool;

    /**
     * @param UserInterface|string $currentUser
     * @param EntityInterface      $subject
     *
     * @return bool
     */
    abstract protected function canEdit(UserInterface | string $currentUser, EntityInterface $subject): bool;

    /**
     * @param UserInterface|string $currentUser
     * @param EntityInterface      $subject
     *
     * @return bool
     */
    abstract protected function canView(UserInterface | string $currentUser, EntityInterface $subject): bool;

    /**
     * @param UserInterface|string $currentUser
     * @param EntityInterface      $subject
     *
     * @return bool
     */
    abstract protected function canDelete(UserInterface | string $currentUser, EntityInterface $subject): bool;
}
