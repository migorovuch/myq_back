<?php

namespace App\Controller;

use App\Model\DTO\User\ChangePasswordDTO;
use App\Model\DTO\User\ResetPasswordDTO;
use App\Model\Manager\UserManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Operation;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ResetPasswordController.
 *
 * @OA\Tag(name="ResetPassword")
 */
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
     * @Operation(description="Reset password request", operationId="api_reset_password_request")
     * @OA\RequestBody(required=true, description="Reset password data", @OA\JsonContent(type="object", ref=@Model(type=ResetPasswordDTO::class)))
     * @OA\Response(response="200", description="Returns nex step description",
     *  @OA\JsonContent(
     *     type="object",
     *     @OA\Property( type="string", property="message", example="Nex step description" ),
     *     @OA\Property( type="string", property="tokenLifetime", example="Reset password token expirtion time" ),
     *     description="Nex step details"
     *  )
     * )
     *
     * @param ResetPasswordDTO    $resetPasswordDTO
     * @param TranslatorInterface $translator
     *
     * @return Response
     *
     * @throws TransportExceptionInterface
     */
    public function request(ResetPasswordDTO $resetPasswordDTO, TranslatorInterface $translator): Response
    {
        $tokenLifetime = $this->userManager->processSendingPasswordResetEmail(
            $resetPasswordDTO->getEmail()
        );

        return $this->response([
            'message' => $translator->trans('Password Reset Email Sent.
An email has been sent that contains a link that you can click to reset your password. This link will expire in %tokenLifetime% hour(s).
If you don\'t receive an email please check your spam folder or try again', ['%tokenLifetime%' => $tokenLifetime]),
            'tokenLifetime' => $tokenLifetime,
        ]);
    }

    /**
     * Validates and process the reset URL that the user clicked in their email.
     *
     * @Rest\Post("/reset", name="reset")
     * @ParamConverter("changePasswordDTO", converter="fos_rest.request_body", options={"deserializationContext"={"validationGroups"="Default"}})
     *
     * @Operation(description="Reset password confirmation", operationId="api_reset_password_reset")
     * @OA\RequestBody(required=true, description="Reset password data", @OA\JsonContent(type="object", ref=@Model(type=ChangePasswordDTO::class)))
     * @OA\Response(response="200", description="Password successfully changed")
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
