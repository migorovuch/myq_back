<?php

namespace App\Controller;

use App\Model\DTO\User\ApproveEmailDTO;
use App\Model\DTO\User\ResetPasswordDTO;
use App\Model\DTO\User\UserDTO;
use App\Model\Manager\UserManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
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
     * @param UserDTO $registrationDTO
     *
     * @return Response
     */
    public function registration(UserDTO $registrationDTO)
    {
        $user = $this->userManager->registration($registrationDTO);

        return $this->response($user, Response::HTTP_OK, ['user']);
    }

    /**
     * @Rest\Post("/approve-email", name="approve_email")
     * @ParamConverter("approveEmailDTO", converter="fos_rest.request_body", options={"deserializationContext"={"validationGroups"="Default"}})
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

    /**
     * @Rest\Get("/user/{id}/app", name="user")
     * @param string $id
     * @return Response
     */
    public function user(string $id)
    {
        return $this->response($this->userManager->find($id), Response::HTTP_OK, ['user']);
    }
}
