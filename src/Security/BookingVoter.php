<?php

namespace App\Security;

use App\Entity\Booking;
use App\Exception\ApiException;
use App\Model\Model\EntityInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class BookingVoter extends AbstractVoter
{
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
        if ($currentUser == 'anon.' && $attributes == static::CREATE) {
            return true;
        }

        return parent::voteOnAttribute($attributes, $subject, $token);
    }

    /**
     * @param UserInterface $currentUser
     * @param Booking $subject
     * @return bool
     */
    protected function canCreate(UserInterface $currentUser, EntityInterface $subject): bool
    {
        return true;
    }

    /**
     * @param UserInterface $currentUser
     * @param Booking $subject
     * @return bool
     */
    protected function canEdit(UserInterface $currentUser, EntityInterface $subject): bool
    {
        return $subject->getSchedule()->getCompany()->getUser()->getId() === $currentUser->getId() ||
            $subject->getUser()->getId() === $currentUser->getId();
    }

    /**
     * @param UserInterface $currentUser
     * @param Booking $subject
     * @return bool
     */
    protected function canView(UserInterface $currentUser, EntityInterface $subject): bool
    {
        return $this->canEdit($currentUser, $subject);
    }

    /**
     * @param UserInterface $currentUser
     * @param Booking $subject
     * @return bool
     */
    protected function canDelete(UserInterface $currentUser, EntityInterface $subject): bool
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
        if (!$subject instanceof Booking) {
            return false;
        }

        return true;
    }
}
