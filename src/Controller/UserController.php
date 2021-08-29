<?php

namespace App\Controller;

use App\Model\DTO\User\ChangeUserDTO;
use App\Model\DTO\User\UserFindDTO;
use App\Model\Manager\UserManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController.
 *
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
}
