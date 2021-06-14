<?php

namespace App\Security;

use App\Entity\User;
use App\Model\Model\EntityInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserVoter.
 */
class UserVoter extends AbstractVoter
{
    /**
     * @param array $attribute
     * @param mixed $subject
     *
     * @return bool
     */
    protected function supports($attribute, $subject)
    {
        if (!\in_array($attribute, [static::CREATE, static::VIEW, static::UPDATE, static::DELETE])) {
            return false;
        }
        if (!$subject instanceof User) {
            return false;
        }

        return true;
    }

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
        if ($attributes === static::CREATE && $currentUser === 'anon.') {
            return true;
        }

        return parent::voteOnAttribute($attributes, $subject, $token);
    }

    /**
     * @param User $user
     *
     * @inheritDoc
     */
    protected function canCreate(UserInterface|string $currentUser, EntityInterface $user): bool
    {
        return true;
    }

    /**
     * @param User $user
     *
     * @inheritDoc
     */
    protected function canView(UserInterface|string $currentUser, EntityInterface $user): bool
    {
        return $this->canEdit($currentUser, $user);
    }

    /**
     * @param User $user
     *
     * @inheritDoc
     */
    protected function canEdit(UserInterface|string $currentUser, EntityInterface $user): bool
    {
        return $currentUser->getId() == $user->getId() || $currentUser->isRole(User::ROLE_ADMIN);
    }

    /**
     * @param User $user
     *
     * @inheritDoc
     */
    protected function canDelete(UserInterface|string $currentUser, EntityInterface $user): bool
    {
        return $this->canEdit($currentUser, $user);
    }
}
