<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Contracts\Translation\TranslatorInterface;

class AuthenticationFailureListener
{
    /**
     * AuthenticationFailureListener constructor.
     */
    public function __construct(protected TranslatorInterface $translator)
    {
    }

    /**
     * @param AuthenticationFailureEvent $event
     */
    public function onAuthenticationFailureResponse(AuthenticationFailureEvent $event)
    {
        $exception = $event->getException();
        $message = $exception->getMessage();
        if (empty($message)) {
            return;
        }
        if ($exception instanceof BadCredentialsException) {
            $message = $this->translator->trans('Invalid credentials.');
        }
        /** @var JWTAuthenticationFailureResponse $response */
        $response = $event->getResponse();
        $response->setMessage($message);
        $event->setResponse($response);
    }
}
