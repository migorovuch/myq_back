<?php

namespace App\Controller;

use App\Model\DTO\Company\CompanyDTO;
use App\Model\Manager\CompanyManagerInterface;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CompanyController.
 *
 * @Route("/companies", name="api_companies_")
 */
class CompanyController extends AbstractBaseController
{
    protected CompanyManagerInterface $companyManager;

    /**
     * CompanyController constructor.
     * @param CompanyManagerInterface $companyManager
     */
    public function __construct(CompanyManagerInterface $companyManager)
    {
        $this->companyManager = $companyManager;
    }

    /**
     * @Rest\Post("/", name="create")
     * @ParamConverter("companyDTO", converter="fos_rest.request_body", options={"deserializationContext"={"validationGroups"="Default"}})
     * @param CompanyDTO $companyDTO
     * @return Response
     */
    public function create(CompanyDTO $companyDTO): Response
    {
        $company = $this->companyManager->create($companyDTO);

        return $this->response($company);
    }

    /**
     * @Rest\Put("/{id}", name="update")
     * @ParamConverter("companyDTO", converter="fos_rest.request_body", options={"deserializationContext"={"validationGroups"="Default"}})
     * @param string $id
     * @param CompanyDTO $companyDTO
     * @return Response
     */
    public function update(string $id, CompanyDTO $companyDTO): Response
    {
        $company = $this->companyManager->update($id, $companyDTO);

        return $this->response($company);
    }

    /**
     * @Rest\Get("/my", name="my_companies")
     * @return Response
     */
    public function myCompanies() : Response
    {
        return $this->response($this->getUser()->getFirstCompany() ?? [], Response::HTTP_OK, ['company']);
    }

    /**
     * @Rest\Get ("/{id}", name="company")
     * @param string $id
     * @return Response
     */
    public function company(string $id) : Response
    {
        $company = $this->companyManager->find($id);
        if (!$company) {
            throw new NotFoundHttpException();
        }

        return $this->response($company);
    }
}
