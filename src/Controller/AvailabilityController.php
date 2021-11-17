<?php

namespace App\Controller;

use App\Model\DTO\Availability\AvailabilityFindDTO;
use App\Model\DTO\SpecialHours\RangeDTO;
use App\Model\Manager\AvailabilityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Operation;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AvailabilityController.
 *
 * @OA\Tag(name="Availability")
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
     * @Operation(description="Schedule availability list", operationId="api_availability_search")
     * @OA\Parameter(
     *     name="filter",
     *     in="query",
     *     description="The filter options",
     *     @OA\Schema(type="object", ref=@Model(type=AvailabilityFindDTO::class))
     * )
     * @OA\Response(response="200", description="Returns availability",
     *  @OA\JsonContent(type="array",
     *      @OA\Items(
     *          type="array",
     *          @OA\Items(
     *              type="object",
     *              ref=@Model(type=RangeDTO::class)
     *          )
     *      ),
     *      description="Error fields")
     * )
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
