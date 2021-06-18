<?php

namespace App\Controller;

use App\Model\DTO\SpecialHours\SpecialHoursDTO;
use App\Model\DTO\SpecialHours\SpecialHoursFindDTO;
use App\Model\Manager\SpecialHoursManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Class SpecialHoursController
 * @Route("/special-hours", name="api_special_hours_")
 */
class SpecialHoursController extends AbstractBaseController
{

    protected SpecialHoursManagerInterface $specialHoursManager;

    /**
     * SpecialHoursController constructor.
     * @param SpecialHoursManagerInterface $specialHoursManager
     */
    public function __construct(SpecialHoursManagerInterface $specialHoursManager)
    {
        $this->specialHoursManager = $specialHoursManager;
    }

    /**
     * @Rest\Get("/search/app", name="search")
     * @ParamConverter("specialHoursFindDTO", converter="query_converter", options={"paramName"="filter", "deserializationContext"={"validationGroups"="Default"}})
     * @param SpecialHoursFindDTO $specialHoursFindDTO
     * @return Response
     */
    public function search(SpecialHoursFindDTO $specialHoursFindDTO): Response
    {
        return $this->response(
            $this->specialHoursManager->findByDTO($specialHoursFindDTO)
        );
    }

    /**
     * @Rest\Put("/update-list", name="update_list")
     * @ParamConverter("list", class="array<App\Model\DTO\SpecialHours\SpecialHoursDTO>", converter="fos_rest.request_body", options={"deserializationContext"={"validationGroups"="Default"}})
     * @param SpecialHoursDTO[] $list
     * @return Response
     */
    public function updateList(array $list): Response
    {
        return $this->response(
            $this->specialHoursManager->updateList($list)
        );
    }

    /**
     * @Rest\Put("/update/{id}", name="update")
     * @ParamConverter("specialHoursDTO", converter="fos_rest.request_body", options={"deserializationContext"={"validationGroups"="Default"}})
     * @param SpecialHoursDTO $specialHoursDTO
     * @return Response
     */
    public function update(string $id, SpecialHoursDTO $specialHoursDTO): Response
    {
        return $this->response(
            $this->specialHoursManager->update($id, $specialHoursDTO)
        );
    }

    /**
     * @Rest\Delete("/{id}", name="delete")
     * @param string $id
     * @return Response
     */
    public function delete(string $id): Response
    {
        $this->specialHoursManager->delete($id);

        return $this->response([]);
    }
}
