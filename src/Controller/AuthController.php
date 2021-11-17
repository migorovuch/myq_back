<?php

namespace App\Controller;

use App\Entity\User;
use App\Model\DTO\Response\Error\ValidationFailed;
use App\Model\DTO\User\ApproveEmailDTO;
use App\Model\DTO\User\RegistrationDTO;
use App\Model\Manager\UserManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Operation;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController.
 *
 * @Route("", name="api_auth_")
 */
class AuthController extends AbstractBaseController
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * AuthController constructor.
     *
     * @param UserManagerInterface $userManager
     */
    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @Rest\Post("/registration", name="registration")
     * @ParamConverter("registrationDTO", converter="fos_rest.request_body", options={"deserializationContext"={"validationGroups"="Default"}})
     *
     * @Operation(description="Registration", operationId="api_auth_registration")
     * @OA\Tag(name="Auth")
     * @OA\RequestBody(required=true, description="Registrations data", @OA\JsonContent(type="object", ref=@Model(type=RegistrationDTO::class)))
     * @OA\Response(response="200", description="Returns Account Data", @OA\JsonContent(type="object", ref=@Model(type=User::class, groups={"user"})))
     * @OA\Response(response="422", description="Validation error data", @OA\JsonContent(type="object",ref=@Model(type=ValidationFailed::class)))
     *
     * @param RegistrationDTO $registrationDTO
     *
     * @return Response
     */
    public function registration(RegistrationDTO $registrationDTO)
    {
        $user = $this->userManager->registration($registrationDTO);

        return $this->response($user, Response::HTTP_OK, ['user']);
    }

    /**
     * @Rest\Post("/approve-email", name="approve_email")
     * @ParamConverter("approveEmailDTO", converter="fos_rest.request_body", options={"deserializationContext"={"validationGroups"="Default"}})
     *
     * @Operation(description="Registration", operationId="api_auth_approve_email")
     * @OA\Tag(name="Auth")
     * @OA\RequestBody(required=true, description="Confirmation data", @OA\JsonContent(type="object", ref=@Model(type=ApproveEmailDTO::class)))
     * @OA\Response(response="200", description="Account approved successfully")
     * @OA\Response(response="422", description="Validation error data", @OA\JsonContent(type="object",ref=@Model(type=ValidationFailed::class)))
     *
     * @param ApproveEmailDTO $approveEmailDTO
     *
     * @return Response
     */
    public function approveEmail(ApproveEmailDTO $approveEmailDTO)
    {
        $this->userManager->approveEmail($approveEmailDTO);

        return $this->response([]);
    }
}
