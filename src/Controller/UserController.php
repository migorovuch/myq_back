<?php

namespace App\Controller;

use App\Entity\User;
use App\Model\DTO\User\ChangeUserDTO;
use App\Model\DTO\User\UserFindDTO;
use App\Model\Manager\UserManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Model\DTO\Response\Error\ValidationFailed;
use Nelmio\ApiDocBundle\Annotation\Security;
use Nelmio\ApiDocBundle\Annotation\Operation;

/**
 * Class UserController.
 *
 * @OA\Tag(name="Users")
 * @Route("/users", name="api_users_")
 */
class UserController extends AbstractBaseController
{
    protected UserManagerInterface $userManager;

    /**
     * UserController constructor.
     */
    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @Rest\Get("/search", name="search")
     * @ParamConverter(
     *     "userFindDTO",
     *     converter="query_converter",
     *     options={"paramName"="filter", "validationGroups"="Default"}
     * )
     *
     * @Operation(description="Search users", operationId="api_users_search")
     * @OA\Parameter(
     *     name="filter",
     *     in="query",
     *     description="The filter options",
     *     @OA\Schema(type="object", ref=@Model(type=UserFindDTO::class))
     * )
     * @OA\Response(response="200", description="Returns list of users",
     *  @OA\JsonContent(
     *     type="object",
     *     @OA\Property( type="number", property="total", example="20" ),
     *     @OA\Property(
     *          type="array",
     *          property="data",
     *          @OA\Items(
     *              type="object",
     *              ref=@Model(type=User::class, groups={"user", "user_status"})
     *          ),
     *          description="Users list"
     *     ),
     *     description="Users list object"
     *  )
     * )
     * @Security(name="Bearer")
     *
     * @param UserFindDTO $userFindDTO
     *
     * @return Response
     */
    public function search(UserFindDTO $userFindDTO): Response
    {
        $data = $this->userManager->findByDTO($userFindDTO);
        $total = $this->userManager->countByDTO($userFindDTO);

        return $this->response(
            [
                'data' => $data,
                'total' => $total,
            ],
            Response::HTTP_OK,
            ['user', 'user_status']
        );
    }

    /**
     * @Rest\Put("/{id}", name="update")
     * @ParamConverter("changeUserDTO", converter="fos_rest.request_body", options={"deserializationContext"={"validationGroups"="Default"}})
     *
     * @Operation(description="Update user by id", operationId="api_users_update")
     * @OA\RequestBody(required=true, description="User data", @OA\JsonContent(type="object", ref=@Model(type=ChangeUserDTO::class)))
     * @OA\Response(response="200", description="Returns updated user data", @OA\JsonContent(type="object", ref=@Model(type=User::class, groups={"user", "user_status"})))
     * @OA\Response(response="422", description="Validation error data", @OA\JsonContent(type="object",ref=@Model(type=ValidationFailed::class)))
     * @Security(name="Bearer")
     *
     * @param string        $id
     * @param ChangeUserDTO $changeUserDTO
     *
     * @return Response
     */
    public function update(string $id, ChangeUserDTO $changeUserDTO): Response
    {
        $data = $this->userManager->change($id, $changeUserDTO);

        return $this->response(
            $data,
            Response::HTTP_OK,
            ['user', 'user_status']
        );
    }

    /**
     * @Rest\Get("/user/{id}/app", name="user")
     *
     * @Operation(description="Company user by id", operationId="api_user_data")
     * @OA\Response(response="200", description="Returns user data", @OA\JsonContent(type="object", ref=@Model(type=User::class, groups={"user"}), description="Company"))
     * @OA\Response(response="404", description="User not found")
     * @Security(name="Bearer")
     *
     * @param string $id
     * @return Response
     */
    public function user(string $id)
    {
        return $this->response($this->userManager->find($id), Response::HTTP_OK, ['user']);
    }
}
