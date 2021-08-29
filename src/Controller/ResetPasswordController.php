<?php

namespace App\Controller;

use App\Model\DTO\User\ChangePasswordDTO;
use App\Model\DTO\User\ResetPasswordDTO;
use App\Model\Manager\UserManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/reset-password')]
class ResetPasswordController extends AbstractBaseController
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @Rest\Post("/request", name="request")
     * @ParamConverter("resetPasswordDTO", converter="fos_rest.request_body", options={"deserializationContext"={"validationGroups"="Default"}})
     *
     * @param ResetPasswordDTO $resetPasswordDTO
     *
     * @return Response
     *
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function request(ResetPasswordDTO $resetPasswordDTO): Response
    {
        $tokenLifetime = $this->userManager->processSendingPasswordResetEmail(
            $resetPasswordDTO->getEmail()
        );

        return $this->response([
            'message' => sprintf(
                'Password Reset Email Sent.
An email has been sent that contains a link that you can click to reset your password. This link will expire in %s hour(s).
If you don\'t receive an email please check your spam folder or try again',
                ($tokenLifetime / 3600)
            ),
            'tokenLifetime' => $tokenLifetime,
        ]);
    }

    /**
     * Validates and process the reset URL that the user clicked in their email.
     *
     * @ParamConverter("changePasswordDTO", converter="fos_rest.request_body", options={"deserializationContext"={"validationGroups"="Default"}})
     * @Rest\Post("/reset", name="reset")
     *
     * @param ChangePasswordDTO $changePasswordDTO
     *
     * @return Response
     */
    public function reset(ChangePasswordDTO $changePasswordDTO): Response
    {
        $this->userManager->resetPassword($changePasswordDTO);

        return $this->response([]);
    }
}
