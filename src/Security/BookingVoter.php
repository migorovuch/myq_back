<?php

namespace App\Security;

use App\Entity\Booking;
use App\Entity\Schedule;
use App\Exception\ApiException;
use App\Model\Model\EntityInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class BookingVoter extends AbstractVoter
{
    /**
     * @param array $attributes
     * @param Booking $subject
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
     * @param Booking $subject
     * @return bool
     */
    protected function canCreate(UserInterface|string $currentUser, EntityInterface $subject): bool
    {
        return $currentUser !== 'anon.' ||
            $subject->getSchedule()->getBookingCondition() === Schedule::BOOKING_CONDITION_ALL_USERS;
    }

    /**
     * @param UserInterface|string $currentUser
     * @param Booking $subject
     * @return bool
     */
    protected function canEdit(UserInterface|string $currentUser, EntityInterface $subject): bool
    {
        return (
                $currentUser !== 'anon.' && (
                    $subject->getSchedule()->getCompany()->getUser()->getId() === $currentUser->getId() ||
                    $subject->getUser()->getId() === $currentUser->getId()
                )
            ) ||
            (
                $currentUser === 'anon.' &&
                !$subject->getUser()
            );
    }

    /**
     * @param UserInterface|string $currentUser
     * @param Booking $subject
     * @return bool
     */
    protected function canView(UserInterface|string $currentUser, EntityInterface $subject): bool
    {
        return $this->canEdit($currentUser, $subject);
    }

    /**
     * @param UserInterface|string $currentUser
     * @param Booking $subject
     * @return bool
     */
    protected function canDelete(UserInterface|string $currentUser, EntityInterface $subject): bool
    {
        return (
            $currentUser !== 'anon.' && (
                $subject->getSchedule()->getCompany()->getUser()->getId() === $currentUser->getId() ||
                $subject->getUser()->getId() === $currentUser->getId()
            )
        );
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
