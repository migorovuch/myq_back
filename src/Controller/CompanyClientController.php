<?php

namespace App\Controller;

use App\Entity\CompanyClient;
use App\Exception\AccessDeniedException;
use App\Model\Manager\CompanyClientManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Model\DTO\Company\CompanyDTO;
use App\Model\DTO\Company\CompanyFindDTO;
use App\Model\Manager\CompanyManagerInterface;
use App\Service\FileUploader;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CompanyClientController
 * @Route("/clients", name="api_clients_")
 */
class CompanyClientController extends AbstractBaseController
{
    protected CompanyClientManagerInterface $companyClientManager;

    /**
     * CompanyClientController constructor.
     */
    public function __construct(CompanyClientManagerInterface $companyClientManager)
    {
        $this->companyClientManager = $companyClientManager;
    }

    /**
     * @Rest\Get ("/{id}/app", name="client")
     * @param string $id
     * @return Response
     */
    public function client(string $id) : Response
    {
        /** @var CompanyClient $client */
        $client = $this->companyClientManager->find($id);
        if (!$client) {
            throw new NotFoundHttpException();
        }
        $currentUser = $this->getUser();
        if (
            $client->getUser() &&
            (
                !$currentUser ||
                ($currentUser && $client->getUser()->getId() !== $currentUser->getId())
            )
        ) {
            throw new AccessDeniedException();
        }

        return $this->response($client, Response::HTTP_OK, ['company_client']);
    }
}
