<?php

namespace App\Controller;

use App\Model\DTO\SpecialHours\SpecialHoursDTO;
use App\Model\DTO\SpecialHours\SpecialHoursFindDTO;
use App\Model\Manager\SpecialHoursManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Model\DTO\Response\Error\ValidationFailed;
use Nelmio\ApiDocBundle\Annotation\Security;
use Nelmio\ApiDocBundle\Annotation\Operation;
use App\Entity\SpecialHours;

/**
 * Class SpecialHoursController.
 *
 * @OA\Tag(name="SpecialHours")
 * @Route("/special-hours", name="api_special_hours_")
 */
class SpecialHoursController extends AbstractBaseController
{
    protected SpecialHoursManagerInterface $specialHoursManager;

    /**
     * SpecialHoursController constructor.
     *
     * @param SpecialHoursManagerInterface $specialHoursManager
     */
    public function __construct(SpecialHoursManagerInterface $specialHoursManager)
    {
        $this->specialHoursManager = $specialHoursManager;
    }

    /**
     * @Rest\Get("/search", name="search")
     * @ParamConverter("specialHoursFindDTO", converter="query_converter", options={"paramName"="filter", "validationGroups"="Default"})
     *
     * @Operation(description="Search speial hours", operationId="api_special_hours_search")
     * @OA\Parameter(
     *     name="filter",
     *     in="query",
     *     description="The filter options",
     *     @OA\Schema(type="object", ref=@Model(type=SpecialHoursFindDTO::class))
     * )
     * @OA\Response(response="200", description="Returns list of special hours",
     *  @OA\JsonContent(
     *     type="object",
     *     @OA\Property( type="number", property="total", example="20" ),
     *     @OA\Property(
     *          type="array",
     *          property="data",
     *          @OA\Items(
     *              type="object",
     *              ref=@Model(type=SpecialHours::class, groups={"special_hours"})
     *          ),
     *          description="Special hours list"
     *     ),
     *     description="Special hours list object"
     *  )
     * )
     * @Security(name="Bearer")
     *
     * @param SpecialHoursFindDTO $specialHoursFindDTO
     *
     * @return Response
     */
    public function search(SpecialHoursFindDTO $specialHoursFindDTO): Response
    {
        return $this->response(
            $this->specialHoursManager->findByDTO($specialHoursFindDTO),
            Response::HTTP_OK,
            ['special_hours']
        );
    }

    /**
     * @Rest\Put("/update-list", name="update_list")
     * @ParamConverter("list", class="array<App\Model\DTO\SpecialHours\SpecialHoursDTO>", converter="fos_rest.request_body", options={"deserializationContext"={"validationGroups"="Default"}})
     *
     * @Operation(description="Update special hours list", operationId="api_special_hours_update_list")
     * @OA\RequestBody(
     *     required=true,
     *     description="Special hours data array",
     *     @OA\JsonContent(
     *          type="array",
     *          @OA\Items(
     *              type="object",
     *              ref=@Model(type=SpecialHoursDTO::class)
     *          ),
     *          description="Special hours data"
     *     )
     * )
     * @OA\Response(response="200", description="Returns updated special hours data", @OA\JsonContent(type="array", @OA\Items(type="object", ref=@Model(type=SpecialHours::class, groups={"special_hours", "special_hours_schedule", "schedule_id"}))))
     * @OA\Response(response="422", description="Validation error data", @OA\JsonContent(type="object",ref=@Model(type=ValidationFailed::class)))
     * @Security(name="Bearer")
     *
     * @param SpecialHoursDTO[] $list
     *
     * @return Response
     */
    public function updateList(array $list): Response
    {
        return $this->response(
            $this->specialHoursManager->updateList($list),
            Response::HTTP_OK,
            ['special_hours', 'special_hours_schedule', 'schedule_id']
        );
    }

    /**
     * @Rest\Put("/update/{id}", name="update")
     * @ParamConverter("specialHoursDTO", converter="fos_rest.request_body", options={"deserializationContext"={"validationGroups"="Default"}})
     *
     * @Operation(description="Update special hours", operationId="api_special_hours_update")
     * @OA\RequestBody(required=true, description="Special hours data", @OA\JsonContent(type="object", ref=@Model(type=SpecialHoursDTO::class)))
     * @OA\Response(response="200", description="Returns updated special hours data", @OA\JsonContent(type="object", ref=@Model(type=SpecialHours::class, groups={"special_hours"})))
     * @OA\Response(response="422", description="Validation error data", @OA\JsonContent(type="object",ref=@Model(type=ValidationFailed::class)))
     * @Security(name="Bearer")
     *
     * @param SpecialHoursDTO $specialHoursDTO
     *
     * @return Response
     */
    public function update(string $id, SpecialHoursDTO $specialHoursDTO): Response
    {
        return $this->response(
            $this->specialHoursManager->update($id, $specialHoursDTO),
            Response::HTTP_OK,
            ['special_hours']
        );
    }

    /**
     * @Rest\Delete("/{id}", name="delete")
     *
     * @Operation(description="Delete special hours", operationId="api_special_hours_delete")
     * @OA\Response(response="200", description="Special hours deleted successfully")
     * @Security(name="Bearer")
     *
     * @param string $id
     *
     * @return Response
     */
    public function delete(string $id): Response
    {
        $this->specialHoursManager->delete($id);

        return $this->response([]);
    }
}
