<?php

namespace App\Controller;

use App\Model\DTO\Schedule\ScheduleDTO;
use App\Model\DTO\Schedule\ScheduleFindDTO;
use App\Model\Manager\ScheduleManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ScheduleController.
 *
 * @Route("/schedule", name="api_schedule_")
 */
class ScheduleController extends AbstractBaseController
{

    protected ScheduleManagerInterface $scheduleManager;

    /**
     * ScheduleController constructor.
     * @param ScheduleManagerInterface $scheduleManager
     */
    public function __construct(ScheduleManagerInterface $scheduleManager)
    {
        $this->scheduleManager = $scheduleManager;
    }


    /**
     * @Rest\Post("/", name="create")
     * @ParamConverter("scheduleDTO", converter="fos_rest.request_body", options={"deserializationContext"={"validationGroups"="Default"}})
     * @param ScheduleDTO $scheduleDTO
     * @return Response
     */
    public function create(ScheduleDTO $scheduleDTO): Response
    {
        $schedule = $this->scheduleManager->create($scheduleDTO);

        return $this->response($schedule, Response::HTTP_OK, ['schedule']);
    }

    /**
     * @Rest\Put("/{id}", name="update")
     * @ParamConverter("scheduleDTO", converter="fos_rest.request_body", options={"deserializationContext"={"validationGroups"="Default"}})
     * @param string $id
     * @param ScheduleDTO $scheduleDTO
     * @return Response
     */
    public function update(string $id, ScheduleDTO $scheduleDTO): Response
    {
        $schedule = $this->scheduleManager->update($id, $scheduleDTO);

        return $this->response($schedule, Response::HTTP_OK, ['schedule']);
    }

    /**
     * @Rest\Get ("/{id}", name="schedule")
     * @param string $id
     * @return Response
     */
    public function schedule(string $id) : Response
    {
        $schedule = $this->scheduleManager->find($id);
        if (!$schedule) {
            throw new NotFoundHttpException();
        }

        return $this->response($schedule, Response::HTTP_OK, ['schedule']);
    }

    /**
     * @Rest\Get("/search/my", name="search_my")
     * @ParamConverter(
     *     "scheduleFindDTO",
     *     converter="query_converter",
     *     options={"paramName"="filter"}
     * )
     * @param ScheduleFindDTO $scheduleFindDTO
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
            ['schedule']
        );
    }

    /**
     * @Rest\Get("/search/app", name="search")
     * @ParamConverter(
     *     "scheduleFindDTO",
     *     converter="query_converter",
     *     options={"paramName"="filter"}
     * )
     * @param ScheduleFindDTO $scheduleFindDTO
     * @return Response
     */
    public function search(ScheduleFindDTO $scheduleFindDTO): Response
    {
        return $this->response(
            $this->scheduleManager->findPublicByDTO($scheduleFindDTO),
            Response::HTTP_OK,
            ['schedule']
        );
    }
}
