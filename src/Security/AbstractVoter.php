<?php

namespace App\Security;

use App\Exception\ApiException;
use App\Model\Model\EntityInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class AbstractVoter implements VoterInterface
{
    const CREATE = 'create';
    const VIEW = 'view';
    const UPDATE = 'update';
    const DELETE = 'delete';

    /**
     * @param TokenInterface $token
     * @param mixed          $subject
     * @param array          $attributes
     *
     * @return int
     */
    public function vote(TokenInterface $token, $subject, array $attributes)
    {
        return $this->voteOnAttribute(reset($attributes), $subject, $token) ? self::ACCESS_GRANTED : self::ACCESS_DENIED;
    }

    /**
     * @param array $attribute
     * @param mixed $subject
     *
     * @return bool
     */
    abstract protected function supports($attribute, $subject);

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
        if ($currentUser !== 'anon.') {
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
     * @param UserInterface   $currentUser
     * @param EntityInterface $schema
     *
     * @return bool
     */
    protected abstract function canCreate(UserInterface $currentUser, EntityInterface $schema): bool;

    /**
     * @param UserInterface   $currentUser
     * @param EntityInterface $schema
     *
     * @return bool
     */
    protected abstract function canEdit(UserInterface $currentUser, EntityInterface $schema): bool;

    /**
     * @param UserInterface   $currentUser
     * @param EntityInterface $schema
     *
     * @return bool
     */
    protected abstract function canView(UserInterface $currentUser, EntityInterface $schema): bool;

    /**
     * @param UserInterface   $currentUser
     * @param EntityInterface $schema
     *
     * @return bool
     */
    protected abstract function canDelete(UserInterface $currentUser, EntityInterface $schema): bool;
}
