<?php

namespace App\Security;

use App\Entity\SpecialHours;
use App\Entity\User;
use App\Model\Model\EntityInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class SpecialHoursVoter extends AbstractVoter
{

    protected function supports($attribute, $subject)
    {
        if (!\in_array($attribute, [static::VIEW, static::CREATE, static::UPDATE, static::DELETE])) {
            return false;
        }
        if (!$subject instanceof SpecialHours) {
            return false;
        }

        return true;
    }

    /**
     * @param UserInterface|string $currentUser
     * @param SpecialHours $subject
     * @return bool
     */
    protected function canCreate(UserInterface|string $currentUser, EntityInterface $subject): bool
    {
        return $subject->getSchedule()->getCompany()->getUser()->getId() === $currentUser->getId() || $currentUser->isRole(User::ROLE_ADMIN);
    }

    /**
     * @param UserInterface|string $currentUser
     * @param SpecialHours $subject
     * @return bool
     */
    protected function canEdit(UserInterface|string $currentUser, EntityInterface $subject): bool
    {
        return $this->canCreate($currentUser, $subject);
    }

    /**
     * @param UserInterface|string $currentUser
     * @param SpecialHours $subject
     * @return bool
     */
    protected function canView(UserInterface|string $currentUser, EntityInterface $subject): bool
    {
        return true;
    }

    /**
     * @param UserInterface|string $currentUser
     * @param SpecialHours $subject
     * @return bool
     */
    protected function canDelete(UserInterface|string $currentUser, EntityInterface $subject): bool
    {
        return $this->canCreate($currentUser, $subject);
    }
}
