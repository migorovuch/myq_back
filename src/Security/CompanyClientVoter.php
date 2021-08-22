<?php

namespace App\Security;

use App\Entity\CompanyClient;
use App\Entity\User;
use App\Exception\ApiException;
use App\Model\Model\EntityInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CompanyClientVoter extends AbstractVoter
{
    /**
     * @param array $attributes
     * @param CompanyClient $subject
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

    /**
     * @param UserInterface|string $currentUser
     * @param CompanyClient $subject
     * @return bool
     */
    protected function canCreate(UserInterface|string $currentUser, EntityInterface $subject): bool
    {
        return true;
    }

    /**
     * @param UserInterface|string $currentUser
     * @param CompanyClient $subject
     * @return bool
     */
    protected function canEdit(UserInterface|string $currentUser, EntityInterface $subject): bool
    {
        return $currentUser->isRole(User::ROLE_ADMIN) ||
            (
                $currentUser !== 'anon.' &&
                $subject->getCompany()->getUser()->getId() === $currentUser->getId()
            );
    }

    /**
     * @param UserInterface|string $currentUser
     * @param CompanyClient $subject
     * @return bool
     */
    protected function canView(UserInterface|string $currentUser, EntityInterface $subject): bool
    {
        return $currentUser->isRole(User::ROLE_ADMIN) ||
            ($currentUser === 'anon.' && !$subject->getUser()) ||
            (
                $currentUser !== 'anon.' &&
                (
                    $subject->getCompany()->getUser()->getId() === $currentUser->getId() ||
                    ($subject->getUser() && $subject->getUser()->getId() === $currentUser->getId())
                )
            );
    }

    /**
     * @param UserInterface|string $currentUser
     * @param CompanyClient $subject
     * @return bool
     */
    protected function canDelete(UserInterface|string $currentUser, EntityInterface $subject): bool
    {
        return false;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports(string $attribute, $subject)
    {
        if (!\in_array($attribute, [static::VIEW, static::CREATE, static::UPDATE, static::DELETE])) {
            return false;
        }
        if (!$subject instanceof CompanyClient) {
            return false;
        }

        return true;
    }
}
