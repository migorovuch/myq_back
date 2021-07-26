<?php

namespace App\EventListener;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthenticationSuccessListener
{
    /**
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        /** @var User $user */
        $user = $event->getUser();
        if (!$user instanceof UserInterface) {
            return;
        }
        $data['data'] = [
            'id' => $user->getId(),
            'fullName' => $user->getFullName(),
            'phone' => $user->getPhone(),
            'nickname' => $user->getNickname(),
        ];

        $event->setData($data);
    }
}
