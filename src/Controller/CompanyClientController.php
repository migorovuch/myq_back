<?php

namespace App\Controller;

use App\Entity\CompanyClient;
use App\Exception\AccessDeniedException;
use App\Model\DTO\CompanyClient\CompanyClientDTO;
use App\Model\DTO\CompanyClient\CompanyClientFindDTO;
use App\Model\Manager\CompanyClientManagerInterface;
use App\Security\CompanyClientVoter;
use http\Client;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\Annotations as Rest;
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
        $this->companyClientManager->denyAccessUnlessGranted(CompanyClientVoter::VIEW, $client);

        return $this->response($client, Response::HTTP_OK, ['company_client']);
    }

    /**
     * @Rest\Get("/search", name="search")
     * @ParamConverter(
     *     "companyClientFindDTO",
     *     converter="query_converter",
     *     options={"paramName"="filter", "validationGroups"="Default"}
     * )
     * @param CompanyClientFindDTO $companyClientFindDTO
     * @return Response
     */
    public function search(CompanyClientFindDTO $companyClientFindDTO): Response
    {
        $client = (new CompanyClient())->setCompany($companyClientFindDTO->getCompany());
        $this->companyClientManager->denyAccessUnlessGranted(CompanyClientVoter::VIEW, $client);

        $data = $this->companyClientManager->findByDTO($companyClientFindDTO);
        $total = $this->companyClientManager->countByDTO($companyClientFindDTO);

        return $this->response(
            [
                'data' => $data,
                'total' => $total,
            ],
            Response::HTTP_OK,
            ['company_client', 'company_client_number_of_bookings', 'company_client_status', 'company_client_pseudonym']
        );
    }

    /**
     * @Rest\Patch ("/{id}", name="change")
     * @ParamConverter("companyClientDTO", converter="fos_rest.request_body", options={"deserializationContext"={"validationGroups"="Default"}})
     * @param CompanyClientDTO $companyClientDTO
     * @return Response
     */
    public function change(string $id, CompanyClientDTO $companyClientDTO)
    {
        return $this->response(
            $this->companyClientManager->update($id, $companyClientDTO),
            Response::HTTP_OK,
            ['company_client', 'company_client_number_of_bookings', 'company_client_status', 'company_client_pseudonym']
        );
    }
}
