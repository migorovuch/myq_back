<?php

namespace App\Controller;

use App\Model\DTO\Availability\AvailabilityFindDTO;
use App\Model\DTO\Schedule\ScheduleFindDTO;
use App\Model\DTO\SpecialHours\SpecialHoursDTO;
use App\Model\DTO\SpecialHours\SpecialHoursFindDTO;
use App\Model\Manager\AvailabilityManagerInterface;
use App\Model\Manager\SpecialHoursManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Class AvailabilityController
 * @Route("/availability", name="api_availability_")
 */
class AvailabilityController extends AbstractBaseController
{
    /**
     * AvailabilityController constructor.
     */
    public function __construct(
        protected AvailabilityManagerInterface $availabilityManager
    ) {}

    /**
     * @Rest\Get("/search/app", name="search")
     * @ParamConverter("availabilityFindDTO", converter="query_converter", options={"paramName"="filter", "deserializationContext"={"validationGroups"="Default"}})
     * @param AvailabilityFindDTO $availabilityFindDTO
     * @return Response
     */
    public function scheduleAvailability(AvailabilityFindDTO $availabilityFindDTO): Response
    {
        return $this->response(
            $this->availabilityManager->findByDTO($availabilityFindDTO)
        );
    }
}
