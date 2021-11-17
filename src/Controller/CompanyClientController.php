<?php

namespace App\Controller;

use App\Entity\CompanyClient;
use App\Exception\EntryNotFoundException;
use App\Model\DTO\CompanyClient\ChangeCompanyClientDTO;
use App\Model\DTO\CompanyClient\CompanyClientFindDTO;
use App\Model\DTO\Response\Error\ValidationFailed;
use App\Model\DTO\User\ChangeUserClientsListDTO;
use App\Model\Manager\CompanyClientManagerInterface;
use App\Security\CompanyClientVoter;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CompanyClientController.
 *
 * @OA\Tag(name="Clients")
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
     * Keep it as it is. Using this endpoint we can check if client has existing account.
     *
     * @Rest\Get ("/{id}/app", name="client_details")
     *
     * @Operation(description="Booking details by id", operationId="api_clients_client_details")
     * @OA\Response(response="200", description="Returns client details",
     *  @OA\JsonContent(
     *     type="object",
     *     ref=@Model(type=CompanyClient::class, groups={"company_client"}),
     *     description="Client"
     *  )
     * )
     * @OA\Response(response="404", description="Client not found")
     *
     * @param string $id
     *
     * @return Response
     */
    public function client(string $id): Response
    {
        /** @var CompanyClient $client */
        $client = $this->companyClientManager->find($id);
        if (!$client) {
            throw new EntryNotFoundException();
        }
        $this->companyClientManager->denyAccessUnlessGranted(CompanyClientVoter::VIEW, $client);

        return $this->response($client, Response::HTTP_OK, ['company_client']);
    }

    /**
     * @Rest\Get("/search", name="search")
     * @ParamConverter(
     *     "companyClientFindDTO",
     *     converter="query_converter",
     *     options={"paramName"="filter", "validationGroups"="Default", "validationGroupsRole"={"ROLE_USER"="client_company_notblank"}}
     * )
     *
     * @Operation(description="Clients search", operationId="api_clients_search")
     * @OA\Parameter(
     *     name="filter",
     *     in="query",
     *     description="The filter options",
     *     @OA\Schema(type="object", ref=@Model(type=CompanyClientFindDTO::class))
     * )
     * @OA\Response(response="200", description="Returns list of clients",
     *  @OA\JsonContent(
     *     type="object",
     *     @OA\Property( type="number", property="total", example="20" ),
     *     @OA\Property(
     *          type="array",
     *          property="data",
     *          @OA\Items(
     *              type="object",
     *              ref=@Model(type=CompanyClient::class, groups={"company_client", "company_client_number_of_bookings", "company_client_status", "company_client_pseudonym", "company_client_company", "company_id", "company_name"})
     *          ),
     *          description="Clients list"
     *     ),
     *     description="Clients list object"
     *  )
     * )
     * @Security(name="Bearer")
     *
     * @param CompanyClientFindDTO $companyClientFindDTO
     *
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
            ['company_client', 'company_client_number_of_bookings', 'company_client_status', 'company_client_pseudonym', 'company_client_company', 'company_id', 'company_name']
        );
    }

    /**
     * @Rest\Patch("/{id}", name="change")
     * @ParamConverter("companyClientDTO", converter="fos_rest.request_body", options={"deserializationContext"={"validationGroups"="Default"}})
     *
     * @Operation(description="Change client params", operationId="api_clients_change")
     * @OA\RequestBody(required=true, description="Client data", @OA\JsonContent(type="object", ref=@Model(type=ChangeCompanyClientDTO::class)))
     * @OA\Response(response="200", description="Returns updated client data", @OA\JsonContent(type="object", ref=@Model(type=CompanyClient::class, groups={"company_client", "company_client_number_of_bookings", "company_client_status", "company_client_pseudonym"})))
     * @OA\Response(response="404", description="Client not found")
     * @OA\Response(response="422", description="Validation error data", @OA\JsonContent(type="object",ref=@Model(type=ValidationFailed::class)))
     * @Security(name="Bearer")
     *
     * @param string                 $id
     * @param ChangeCompanyClientDTO $companyClientDTO
     *
     * @return Response
     */
    public function change(string $id, ChangeCompanyClientDTO $companyClientDTO)
    {
        return $this->response(
            $this->companyClientManager->change($id, $companyClientDTO),
            Response::HTTP_OK,
            ['company_client', 'company_client_number_of_bookings', 'company_client_status', 'company_client_pseudonym']
        );
    }

    /**
     * @Rest\Post("/update-clients", name="update_clients_relations")
     * @ParamConverter("changeUserClientsListDTO", converter="fos_rest.request_body", options={"deserializationContext"={"validationGroups"="Default"}})
     *
     * @Operation(description="Merge clients without account with axisting account (after login action)", operationId="api_clients_update_clients_relations")
     * @OA\RequestBody(required=true, description="Clients id list", @OA\JsonContent(type="object", ref=@Model(type=ChangeUserClientsListDTO::class)))
     * @OA\Response(response="200", description="Clients succesfully merged")
     * @Security(name="Bearer")
     */
    public function updateUserClientsList(ChangeUserClientsListDTO $changeUserClientsListDTO): Response
    {
        $this->companyClientManager->updateUserClientsList($changeUserClientsListDTO->getClients());

        return $this->response([]);
    }
}
