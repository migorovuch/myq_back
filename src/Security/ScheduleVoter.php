<?php

namespace App\Security;

use App\Entity\Schedule;
use App\Entity\User;
use App\Model\Model\EntityInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ScheduleVoter extends AbstractVoter
{

    /**
     * @param UserInterface|string $currentUser
     * @param Schedule $subject
     * @return bool
     */
    protected function canCreate(UserInterface|string $currentUser, EntityInterface $subject): bool
    {
        return true;
    }

    /**
     * @param UserInterface|string $currentUser
     * @param Schedule $subject
     * @return bool
     */
    protected function canEdit(UserInterface|string $currentUser, EntityInterface $subject): bool
    {
        return $subject->getCompany()->getUser()->getId() === $currentUser->getId() || $currentUser->isRole(User::ROLE_ADMIN);
    }

    /**
     * @param UserInterface|string $currentUser
     * @param Schedule $subject
     * @return bool
     */
    protected function canView(UserInterface|string $currentUser, EntityInterface $subject): bool
    {
        return $this->canEdit($currentUser, $subject) || $subject->getEnabled();
    }

    /**
     * @param UserInterface|string $currentUser
     * @param Schedule $subject
     * @return bool
     */
    protected function canDelete(UserInterface|string $currentUser, EntityInterface $subject): bool
    {
        return $this->canEdit($currentUser, $subject);
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
        if (!$subject instanceof Schedule) {
            return false;
        }

        return true;
    }
}
