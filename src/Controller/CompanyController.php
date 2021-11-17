<?php

namespace App\Controller;

use App\Exception\EntryNotFoundException;
use App\Model\DTO\Company\CompanyDTO;
use App\Model\DTO\Company\CompanyFindDTO;
use App\Model\Manager\CompanyManagerInterface;
use App\Service\FileUploader;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Model\DTO\Response\Error\ValidationFailed;
use App\Entity\Company;
use Nelmio\ApiDocBundle\Annotation\Security;
use Nelmio\ApiDocBundle\Annotation\Operation;

/**
 * Class CompanyController.
 *
 * @OA\Tag(name="Companies")
 * @Route("/companies", name="api_companies_")
 */
class CompanyController extends AbstractBaseController
{
    protected CompanyManagerInterface $companyManager;

    /**
     * CompanyController constructor.
     *
     * @param CompanyManagerInterface $companyManager
     */
    public function __construct(CompanyManagerInterface $companyManager)
    {
        $this->companyManager = $companyManager;
    }

    /**
     * @Rest\Post("/", name="create")
     * @ParamConverter("companyDTO", converter="fos_rest.request_body", options={"deserializationContext"={"validationGroups"="Default"}})
     *
     * @Operation(description="Create new company", operationId="api_companies_create")
     * @OA\RequestBody(required=true, description="Company data", @OA\JsonContent(type="object", ref=@Model(type=CompanyDTO::class)))
     * @OA\Response(response="200", description="Returns new company data", @OA\JsonContent(type="object", ref=@Model(type=Company::class, groups={"company"})))
     * @OA\Response(response="422", description="Validation error data", @OA\JsonContent(type="object",ref=@Model(type=ValidationFailed::class)))
     * @Security(name="Bearer")
     *
     * @param CompanyDTO $companyDTO
     *
     * @return Response
     */
    public function create(CompanyDTO $companyDTO): Response
    {
        $company = $this->companyManager->create($companyDTO);

        return $this->response($company, Response::HTTP_OK, ['company']);
    }

    /**
     * @Rest\Put("/{id}", name="update")
     * @ParamConverter("companyDTO", converter="fos_rest.request_body", options={"deserializationContext"={"validationGroups"="Default"}})
     *
     * @Operation(description="Update company", operationId="api_companies_update")
     * @OA\RequestBody(required=true, description="Company data", @OA\JsonContent(type="object", ref=@Model(type=CompanyDTO::class)))
     * @OA\Response(response="200", description="Returns company data", @OA\JsonContent(type="object", ref=@Model(type=Company::class, groups={"company", "company_access_token"})))
     * @OA\Response(response="422", description="Validation error data", @OA\JsonContent(type="object",ref=@Model(type=ValidationFailed::class)))
     * @Security(name="Bearer")
     *
     * @param string     $id
     * @param CompanyDTO $companyDTO
     *
     * @return Response
     */
    public function update(string $id, CompanyDTO $companyDTO): Response
    {
        $company = $this->companyManager->update($id, $companyDTO);

        return $this->response($company, Response::HTTP_OK, ['company', 'company_access_token']);
    }

    /**
     * @Rest\Get("/my", name="my_companies")
     *
     * @Operation(description="Get company current user company data", operationId="api_companies_my")
     * @OA\Response(response="200", description="Returns user company data", @OA\JsonContent(type="object", ref=@Model(type=Company::class, groups={"company", "company_access_token"})))
     * @Security(name="Bearer")
     *
     * @return Response
     */
    public function myCompanies(): Response
    {
        return $this->response($this->getUser()->getFirstCompany() ?? [], Response::HTTP_OK, ['company', 'company_access_token']);
    }

    /**
     * @Rest\Get ("/{id}", name="company")
     *
     * @Operation(description="Company details by id", operationId="api_companies_company_details")
     * @OA\Response(response="200", description="Returns company data", @OA\JsonContent(type="object", ref=@Model(type=Company::class, groups={"company"}), description="Company"))
     * @OA\Response(response="404", description="Company not found")
     * @Security(name="Bearer")
     *
     * @param string $id
     *
     * @return Response
     */
    public function company(string $id): Response
    {
        $company = $this->companyManager->find($id);
        if (!$company) {
            throw new EntryNotFoundException();
        }

        return $this->response($company, Response::HTTP_OK, ['company']);
    }

    /**
     * @Rest\Post("/logo/{id}", name="company_logo")
     *
     * @Operation(description="Upload company logo", operationId="api_companies_upload_logo")
     * @OA\Parameter(
     *     @OA\Schema(type="string", format="binary"),
     *     name="logo",
     *     description="Company logo",
     *     in="query",
     * )
     * @OA\Response(response="200", description="Returns uploaded file name", @OA\JsonContent(type="object", @OA\Property( type="string", property="fileName", example="/company/logo_file_name.jpg" ), description="Company"))
     * @Security(name="Bearer")
     *
     * @param string  $id
     * @param Request $request
     *
     * @return Response
     */
    public function uploadLogo(string $id, Request $request, FileUploader $fileUploader): Response
    {
        $files = $request->files->get('logo');
        $fileName = $fileUploader->upload(
            $files,
            '/company',
            'logo_'.$id
        );
        $this->companyManager->change($id, new CompanyDTO(null, null, null, null, null, null, $fileName));

        return $this->response(['fileName' => $fileName]);
    }

    /**
     * @Rest\Get("/search/app", name="search_public")
     * @ParamConverter(
     *     "companyFindDTO",
     *     converter="query_converter",
     *     options={"paramName"="filter"}
     * )
     *
     * @Operation(description="Search public companies", operationId="api_companies_search_public")
     * @OA\Parameter(
     *     name="filter",
     *     in="query",
     *     description="The filter options",
     *     @OA\Schema(type="object", ref=@Model(type=CompanyFindDTO::class))
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
     *              ref=@Model(type=Company::class, groups={"company", "company_user", "user_id"})
     *          ),
     *          description="Public companies list"
     *     ),
     *     description="Public companies list object"
     *  )
     * )
     *
     * @param CompanyFindDTO $companyFindDTO
     *
     * @return Response
     */
    public function searchPublic(CompanyFindDTO $companyFindDTO): Response
    {
        $data = $this->companyManager->findPublicByDTO($companyFindDTO);
        $total = $this->companyManager->countByDTO($companyFindDTO);

        return $this->response(
            [
                'data' => $data,
                'total' => $total,
            ],
            Response::HTTP_OK,
            ['company', 'company_user', 'user_id']
        );
    }

    /**
     * @Rest\Get("/search/all", name="search")
     * @ParamConverter(
     *     "companyFindDTO",
     *     converter="query_converter",
     *     options={"paramName"="filter"}
     * )
     *
     * @Operation(description="Search companies", operationId="api_companies_search")
     * @OA\Parameter(
     *     name="filter",
     *     in="query",
     *     description="The filter options",
     *     @OA\Schema(type="object", ref=@Model(type=CompanyFindDTO::class))
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
     *              ref=@Model(type=Company::class, groups={"company", "company_created", "company_updated", "company_user", "user_id"})
     *          ),
     *          description="Companies list"
     *     ),
     *     description="Companies list object"
     *  )
     * )
     *
     * @param CompanyFindDTO $companyFindDTO
     *
     * @return Response
     */
    public function search(CompanyFindDTO $companyFindDTO): Response
    {
        $data = $this->companyManager->findByDTO($companyFindDTO);
        $total = $this->companyManager->countByDTO($companyFindDTO);

        return $this->response(
            [
                'data' => $data,
                'total' => $total,
            ],
            Response::HTTP_OK,
            ['company', 'company_created', 'company_updated', 'company_user', 'user_id']
        );
    }
}
