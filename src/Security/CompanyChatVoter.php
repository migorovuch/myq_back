<?php

namespace App\Security;

use App\Entity\CompanyChat;
use App\Exception\ApiException;
use App\Model\Model\EntityInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CompanyChatVoter extends AbstractVoter
{
    protected function canCreate(UserInterface | string $currentUser, EntityInterface $subject): bool
    {
        return true;
    }

    protected function canEdit(UserInterface | string $currentUser, EntityInterface $subject): bool
    {
        return true;
    }

    protected function canView(UserInterface | string $currentUser, EntityInterface $subject): bool
    {
        return true;
    }

    protected function canDelete(UserInterface | string $currentUser, EntityInterface $subject): bool
    {
        return true;
    }
    /**
     * @param array          $attributes
     * @param CompanyChat        $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attributes, $subject, TokenInterface $token)
    {
        $currentUser = $token->getUser();
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

    protected function supports(string $attribute, $subject)
    {
        if (!\in_array($attribute, [static::VIEW, static::CREATE, static::UPDATE, static::DELETE])) {
            return false;
        }
        if (!$subject instanceof CompanyChat) {
            return false;
        }

        return true;
    }
}
