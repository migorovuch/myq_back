<?php

namespace App\Controller;

use App\Model\DTO\Schedule\ScheduleDTO;
use App\Model\DTO\Schedule\ScheduleFindDTO;
use App\Model\Manager\ScheduleManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Model\DTO\Response\Error\ValidationFailed;
use Nelmio\ApiDocBundle\Annotation\Security;
use Nelmio\ApiDocBundle\Annotation\Operation;
use App\Entity\Schedule;

/**
 * Class ScheduleController.
 *
 * @OA\Tag(name="Schedules")
 * @Route("/schedule", name="api_schedule_")
 */
class ScheduleController extends AbstractBaseController
{
    protected ScheduleManagerInterface $scheduleManager;

    /**
     * ScheduleController constructor.
     *
     * @param ScheduleManagerInterface $scheduleManager
     */
    public function __construct(ScheduleManagerInterface $scheduleManager)
    {
        $this->scheduleManager = $scheduleManager;
    }

    /**
     * @Rest\Post("/", name="create")
     * @ParamConverter("scheduleDTO", converter="fos_rest.request_body", options={"deserializationContext"={"validationGroups"="Default"}})
     *
     * @Operation(description="Create new schedule", operationId="api_schedule_create")
     * @OA\RequestBody(required=true, description="Schedule data", @OA\JsonContent(type="object", ref=@Model(type=ScheduleDTO::class)))
     * @OA\Response(response="200", description="Returns new schedule data", @OA\JsonContent(type="object", ref=@Model(type=Schedule::class, groups={"schedule", "schedule_company", "company_id"})))
     * @OA\Response(response="422", description="Validation error data", @OA\JsonContent(type="object",ref=@Model(type=ValidationFailed::class)))
     * @Security(name="Bearer")
     *
     * @param ScheduleDTO $scheduleDTO
     *
     * @return Response
     */
    public function create(ScheduleDTO $scheduleDTO): Response
    {
        $schedule = $this->scheduleManager->create($scheduleDTO);

        return $this->response($schedule, Response::HTTP_OK, ['schedule', 'schedule_company', 'company_id']);
    }

    /**
     * @Rest\Put("/{id}", name="update")
     * @ParamConverter("scheduleDTO", converter="fos_rest.request_body", options={"deserializationContext"={"validationGroups"="Default"}})
     *
     * @Operation(description="Update schedule", operationId="api_schedule_update")
     * @OA\RequestBody(required=true, description="Schedule data", @OA\JsonContent(type="object", ref=@Model(type=ScheduleDTO::class)))
     * @OA\Response(response="200", description="Returns updated schedule data", @OA\JsonContent(type="object", ref=@Model(type=Schedule::class, groups={"schedule", "schedule_company", "company_id"})))
     * @OA\Response(response="422", description="Validation error data", @OA\JsonContent(type="object",ref=@Model(type=ValidationFailed::class)))
     * @Security(name="Bearer")
     *
     * @param string      $id
     * @param ScheduleDTO $scheduleDTO
     *
     * @return Response
     */
    public function update(string $id, ScheduleDTO $scheduleDTO): Response
    {
        $schedule = $this->scheduleManager->update($id, $scheduleDTO);

        return $this->response($schedule, Response::HTTP_OK, ['schedule', 'schedule_company', 'company_id']);
    }

    /**
     * @Rest\Get ("/{id}", name="schedule")
     *
     * @Operation(description="Company schedule by id", operationId="api_schedule_data")
     * @OA\Response(response="200", description="Returns schedule data", @OA\JsonContent(type="object", ref=@Model(type=Schedule::class, groups={"schedule", "schedule_company", "company_id"}), description="Company"))
     * @OA\Response(response="404", description="Schedule not found")
     * @Security(name="Bearer")
     *
     * @param string $id
     *
     * @return Response
     */
    public function schedule(string $id): Response
    {
        $schedule = $this->scheduleManager->find($id);
        if (!$schedule) {
            throw new NotFoundHttpException();
        }

        return $this->response($schedule, Response::HTTP_OK, ['schedule', 'schedule_company', 'company_id']);
    }

    /**
     * @Rest\Get("/search/my", name="search_my")
     * @ParamConverter(
     *     "scheduleFindDTO",
     *     converter="query_converter",
     *     options={"paramName"="filter"}
     * )
     *
     * @Operation(description="Search my schedules", operationId="api_schedule_search_my")
     * @OA\Parameter(
     *     name="filter",
     *     in="query",
     *     description="The filter options",
     *     @OA\Schema(type="object", ref=@Model(type=ScheduleFindDTO::class))
     * )
     * @OA\Response(response="200", description="Returns list of schedules for logged user",
     *  @OA\JsonContent(
     *     type="object",
     *     @OA\Property( type="number", property="total", example="20" ),
     *     @OA\Property(
     *          type="array",
     *          property="data",
     *          @OA\Items(
     *              type="object",
     *              ref=@Model(type=Schedule::class, groups={"schedule", "schedule_company", "company_id"})
     *          ),
     *          description="My schedules list"
     *     ),
     *     description="My schedules list object"
     *  )
     * )
     * @Security(name="Bearer")
     *
     * @param ScheduleFindDTO $scheduleFindDTO
     *
     * @return Response
     */
    public function searchMy(ScheduleFindDTO $scheduleFindDTO): Response
    {
        $scheduleFindDTO = new ScheduleFindDTO(
            $scheduleFindDTO->getId(),
            $this->getUser()->getFirstCompany(),
            $scheduleFindDTO->getName(),
            $scheduleFindDTO->getEnabled(),
            $scheduleFindDTO->getSort(),
            $scheduleFindDTO->getPage(),
            $scheduleFindDTO->getCondition()
        );

        return $this->response(
            $this->scheduleManager->findByDTO($scheduleFindDTO),
            Response::HTTP_OK,
            ['schedule', 'schedule_company', 'company_id']
        );
    }

    /**
     * @Rest\Get("/search/app", name="search")
     * @ParamConverter(
     *     "scheduleFindDTO",
     *     converter="query_converter",
     *     options={"paramName"="filter"}
     * )
     *
     * @Operation(description="Search schedules", operationId="api_schedule_search")
     * @OA\Parameter(
     *     name="filter",
     *     in="query",
     *     description="The filter options",
     *     @OA\Schema(type="object", ref=@Model(type=ScheduleFindDTO::class))
     * )
     * @OA\Response(response="200", description="Returns list of schedules",
     *  @OA\JsonContent(
     *     type="object",
     *     @OA\Property( type="number", property="total", example="20" ),
     *     @OA\Property(
     *          type="array",
     *          property="data",
     *          @OA\Items(
     *              type="object",
     *              ref=@Model(type=Schedule::class, groups={"schedule", "schedule_company", "company_id"})
     *          ),
     *          description="Schedules list"
     *     ),
     *     description="Schedules list object"
     *  )
     * )
     * @Security(name="Bearer")
     *
     * @param ScheduleFindDTO $scheduleFindDTO
     *
     * @return Response
     */
    public function search(ScheduleFindDTO $scheduleFindDTO): Response
    {
        return $this->response(
            $this->scheduleManager->findPublicByDTO($scheduleFindDTO),
            Response::HTTP_OK,
            ['schedule', 'schedule_company', 'company_id']
        );
    }
}
