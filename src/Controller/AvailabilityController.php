<?php

namespace App\Controller;

use App\Model\DTO\Availability\AvailabilityFindDTO;
use App\Model\Manager\AvailabilityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AvailabilityController.
 *
 * @Route("/availability", name="api_availability_")
 */
class AvailabilityController extends AbstractBaseController
{
    /**
     * AvailabilityController constructor.
     */
    public function __construct(
        protected AvailabilityManagerInterface $availabilityManager
    ) {
    }

    /**
     * @Rest\Get("/search/app", name="search")
     * @ParamConverter("availabilityFindDTO", converter="query_converter", options={"paramName"="filter", "validationGroups"="Default"})
     *
     * @param AvailabilityFindDTO $availabilityFindDTO
     *
     * @return Response
     */
    public function scheduleAvailability(AvailabilityFindDTO $availabilityFindDTO): Response
    {
        return $this->response(
            $this->availabilityManager->findByDTO($availabilityFindDTO)
        );
    }
}
