<?php

namespace App\Controller;

use App\Model\DTO\User\ChangeAccountDTO;
use App\Model\Manager\UserManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AccountController
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
     * @Rest\Patch("/", name="change_my")
     * @ParamConverter("changeUserDTO", converter="fos_rest.request_body", options={"deserializationContext"={"validationGroups"="Default"}})
     * @param ChangeAccountDTO $changeUserDTO
     * @return Response
     */
    public function changeMy(ChangeAccountDTO $changeUserDTO): Response
    {
        $user = $this->userManager->changeAccount($changeUserDTO);

        return $this->response($user, Response::HTTP_OK, ['user_id', 'user_phone', 'user_nickname', 'user_email', 'user_fullname']);
    }
}
