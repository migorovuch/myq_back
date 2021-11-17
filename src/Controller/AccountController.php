<?php

namespace App\Controller;

use App\Entity\User;
use App\Model\DTO\Response\Error\ValidationFailed;
use App\Model\DTO\User\ChangeAccountDTO;
use App\Model\Manager\UserManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AccountController.
 *
 * @OA\Tag(name="Account")
 * @Route("/account", name="api_account_")
 */
class AccountController extends AbstractBaseController
{
    protected UserManagerInterface $userManager;

    /**
     * AccountController constructor.
     */
    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @Rest\Put("/", name="change_my")
     * @ParamConverter("changeUserDTO", converter="fos_rest.request_body", options={"deserializationContext"={"groups"={"user_id", "user_phone", "user_nickname", "user_password", "user_old_password", "user_email", "user_fullname"}, "validationGroups"="Default"}})
     *
     * @Operation(description="Change account data", operationId="api_account_change")
     * @OA\RequestBody(
     *     description="Update account data",
     *     required=true,
     *     @OA\JsonContent(
     *         type="object",
     *         ref=@Model(type=ChangeAccountDTO::class, groups={"user_phone", "user_nickname", "user_password", "user_old_password", "user_email", "user_fullname"})
     *     )
     * )
     * @OA\Response(
     *      response="200",
     *      description="Returns Account Data",
     *      @OA\JsonContent(
     *          type="object",
     *          ref=@Model(type=User::class, groups={"user_id", "user_phone", "user_nickname", "user_password", "user_old_password", "user_email", "user_fullname"})
     *      )
     * )
     * @OA\Response(response="422", description="Validation error data", @OA\JsonContent(type="object",ref=@Model(type=ValidationFailed::class)))
     * @Security(name="Bearer")
     *
     * @param ChangeAccountDTO $changeUserDTO
     *
     * @return Response
     */
    public function changeMy(ChangeAccountDTO $changeUserDTO): Response
    {
        $user = $this->userManager->changeAccount($changeUserDTO);

        return $this->response($user, Response::HTTP_OK, ['user_id', 'user_phone', 'user_nickname', 'user_email', 'user_fullname']);
    }
}
