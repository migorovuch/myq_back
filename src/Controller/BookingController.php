<?php

namespace App\Controller;

use App\Exception\EntryNotFoundException;
use App\Model\DTO\Booking\BookingDTO;
use App\Model\DTO\Booking\BookingFindDTO;
use App\Model\DTO\Booking\ChangeBookingDTO;
use App\Model\Manager\BookingManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Model\DTO\Response\Error\ValidationFailed;
use App\Entity\Booking;
use Nelmio\ApiDocBundle\Annotation\Security;
use Nelmio\ApiDocBundle\Annotation\Operation;

/**
 * Class BookingController.
 *
 * @OA\Tag(name="Bookings")
 * @Route("/bookings", name="api_bookings_")
 */
class BookingController extends AbstractBaseController
{
    protected BookingManagerInterface $bookingManager;
    protected array $serializeGroups;

    /**.
     * @param BookingManagerInterface $bookingManager
     */
    public function __construct(BookingManagerInterface $bookingManager)
    {
        $this->bookingManager = $bookingManager;
        $this->serializeGroups = [
            'booking',
            'booking_schedule',
            'schedule_id',
            'schedule_name',
            'schedule_description',
            'schedule_booking_duration',
            'schedule_min_booking_time',
            'schedule_max_booking_time',
            'schedule_company',
            'company_id',
            'company_name',
        ];
    }

    /**
     * @Rest\Post("/", name="create")
     * @ParamConverter("bookingDTO", converter="fos_rest.request_body", options={"deserializationContext"={"validationGroups"="Default"}})
     *
     * @Operation(description="Create booking", operationId="api_bookings_create")
     * @OA\RequestBody(required=true, description="Booking data", @OA\JsonContent(type="object", ref=@Model(type=BookingDTO::class, groups={"booking_schedule", "booking_start", "booking_end", "booking_title", "booking_comment", "booking_client", "booking_client_name", "booking_client_phone", "booking_new_client"})))
     * @OA\Response(response="200", description="Returns new booking data", @OA\JsonContent(type="object", ref=@Model(type=Booking::class, groups={"booking", "booking_schedule", "schedule_id", "schedule_name", "schedule_description", "schedule_booking_duration", "schedule_min_booking_time", "schedule_max_booking_time", "schedule_company", "company_id", "company_name", "company_client", "booking_client"})))
     * @OA\Response(response="422", description="Validation error data", @OA\JsonContent(type="object",ref=@Model(type=ValidationFailed::class)))
     *
     * @param BookingDTO $bookingDTO
     *
     * @return Response
     */
    public function create(BookingDTO $bookingDTO): Response
    {
        $booking = $this->bookingManager->create($bookingDTO);
        $serializationGroups = array_merge($this->serializeGroups, [
            'company_client',
            'booking_client',
        ]);

        return $this->response(
            $booking,
            Response::HTTP_OK,
            $serializationGroups
        );
    }

    /**
     * @Rest\Put("/{id}", name="update")
     * @ParamConverter("bookingDTO", converter="fos_rest.request_body", options={"deserializationContext"={"validationGroups"="Default"}})
     *
     * @Operation(description="Update booking (Only for company managers)", operationId="api_bookings_update")
     * @OA\RequestBody(required=true, description="Booking data", @OA\JsonContent(type="object", ref=@Model(type=BookingDTO::class, groups={"booking_schedule", "booking_start", "booking_end", "booking_title", "booking_comment", "booking_client", "booking_client_name", "booking_client_phone", "booking_new_client"})))
     * @OA\Response(response="200", description="Returns updated booking data", @OA\JsonContent(type="object", ref=@Model(type=Booking::class, groups={"booking", "booking_schedule", "schedule_id", "schedule_name", "schedule_description", "schedule_booking_duration", "schedule_min_booking_time", "schedule_max_booking_time", "schedule_company", "company_id", "company_name", "booking_title", "company_client", "booking_client", "company_client_pseudonym"})))
     * @OA\Response(response="404", description="Booking not found")
     * @OA\Response(response="422", description="Validation error data", @OA\JsonContent(type="object",ref=@Model(type=ValidationFailed::class)))
     * @Security(name="Bearer")
     *
     * @param string     $id
     * @param BookingDTO $bookingDTO
     *
     * @return Response
     */

    public function update(string $id, BookingDTO $bookingDTO): Response
    {
        $booking = $this->bookingManager->update($id, $bookingDTO);
        $serializationGroups = array_merge($this->serializeGroups, [
                'booking_title',
                'company_client',
                'booking_client',
                'company_client_pseudonym',
            ]);

        return $this->response($booking, Response::HTTP_OK, $serializationGroups);
    }

    /**
     * @Rest\Patch("/{id}", name="change")
     * @ParamConverter("changeBookingDTO", converter="fos_rest.request_body", options={"deserializationContext"={"validationGroups"="Default"}})
     *
     * @Operation(description="Change booking params", operationId="api_bookings_change")
     * @OA\RequestBody(required=true, description="Booking data", @OA\JsonContent(type="object", ref=@Model(type=ChangeBookingDTO::class, groups={"booking_schedule", "booking_start", "booking_end", "booking_title", "booking_comment", "booking_client", "booking_client_name", "booking_client_phone", "booking_new_client"})))
     * @OA\Response(response="200", description="Returns updated booking data", @OA\JsonContent(type="object", ref=@Model(type=Booking::class, groups={"booking", "booking_schedule", "schedule_id", "schedule_name", "schedule_description", "schedule_booking_duration", "schedule_min_booking_time", "schedule_max_booking_time", "schedule_company", "company_id", "company_name", "booking_title", "company_client", "booking_client", "company_client_pseudonym"})))
     * @OA\Response(response="404", description="Booking not found")
     * @OA\Response(response="422", description="Validation error data", @OA\JsonContent(type="object",ref=@Model(type=ValidationFailed::class)))
     * @Security(name="Bearer")
     *
     * @param string           $id
     * @param ChangeBookingDTO $changeBookingDTO
     *
     * @return Response
     */
    public function change(string $id, ChangeBookingDTO $changeBookingDTO): Response
    {
        $booking = $this->bookingManager->change($id, $changeBookingDTO);
        $serializationGroups = array_merge($this->serializeGroups, [
                'booking_title',
                'company_client',
                'booking_client',
                'company_client_pseudonym',
            ]);

        return $this->response($booking, Response::HTTP_OK, $serializationGroups);
    }

    /**
     * @Rest\Get("/search", name="search")
     * @ParamConverter(
     *     "bookingFindDTO",
     *     converter="query_converter",
     *     options={"paramName"="filter", "validationGroups"="Default", "validationGroupsRole"={"ROLE_USER"="booking_company"}}
     * )
     *
     * @Operation(description="Bookings search", operationId="api_bookings_search")
     * @OA\Parameter(
     *     name="filter",
     *     in="query",
     *     description="The filter options",
     *     @OA\Schema(type="object", ref=@Model(type=BookingFindDTO::class))
     * )
     * @OA\Response(response="200", description="Returns list of bookings",
     *  @OA\JsonContent(
     *     type="object",
     *     @OA\Property( type="number", property="total", example="20" ),
     *     @OA\Property(
     *          type="array",
     *          property="data",
     *          @OA\Items(
     *              type="object",
     *              ref=@Model(type=Booking::class, groups={"booking", "booking_schedule", "schedule_id", "schedule_name", "schedule_description", "schedule_booking_duration", "schedule_min_booking_time", "schedule_max_booking_time", "schedule_company", "company_id", "company_name", "booking_title", "company_client", "booking_client", "company_client_pseudonym"})
     *          ),
     *          description="Bookings list"
     *     ),
     *     description="Bookings list object"
     *  )
     * )
     * @Security(name="Bearer")
     *
     * @param BookingFindDTO $bookingFindDTO
     *
     * @return Response
     */
    public function search(BookingFindDTO $bookingFindDTO): Response
    {
        $data = $this->bookingManager->findByDTO($bookingFindDTO);
        $total = $this->bookingManager->countByDTO($bookingFindDTO);
        $serializationGroups = array_merge($this->serializeGroups, [
                'booking_title',
                'company_client',
                'booking_client',
                'company_client_pseudonym',
            ]);

        return $this->response(
            [
                'data' => $data,
                'total' => $total
            ],
            Response::HTTP_OK,
            $serializationGroups
        );
    }

    /**
     * @Rest\Get("/my", name="my")
     * @ParamConverter(
     *     "bookingFindDTO",
     *     converter="query_converter",
     *     options={"paramName"="filter", "validationGroups"="Default"}
     * )
     *
     * @Operation(description="Current user bookings", operationId="api_bookings_my")
     * @OA\Parameter(
     *     name="filter",
     *     in="query",
     *     description="The filter options",
     *     @OA\Schema(type="object", ref=@Model(type=BookingFindDTO::class))
     * )
     * @OA\Response(response="200", description="Returns bookings list",
     *  @OA\JsonContent(
     *     type="object",
     *     @OA\Property( type="number", property="total", example="20" ),
     *     @OA\Property(
     *          type="array",
     *          property="data",
     *          @OA\Items(
     *              type="object",
     *              ref=@Model(type=Booking::class, groups={"booking", "booking_schedule", "schedule_id", "schedule_name", "schedule_description", "schedule_booking_duration", "schedule_min_booking_time", "schedule_max_booking_time", "schedule_company", "company_id", "company_name", "company_client", "booking_client"})
     *          ),
     *          description="Bookings list"
     *     ),
     *     description="Bookings list object"
     *  )
     * )
     *
     * @param BookingFindDTO $bookingFindDTO
     *
     * @return Response
     */
    public function myBookings(BookingFindDTO $bookingFindDTO): Response
    {
        $bookingFindDTO = $this->bookingManager->buildMyBookingFindDTO($bookingFindDTO);
        $result = $this->bookingManager->findByDTO($bookingFindDTO);
        $total = $this->bookingManager->countByDTO($bookingFindDTO);
        $serializationGroups = array_merge($this->serializeGroups, [
                'company_client',
                'booking_client',
            ]);

        return $this->response(
            [
                'data' => $result,
                'total' => $total,
            ],
            Response::HTTP_OK,
            $serializationGroups
        );
    }

    /**
     * @Rest\Get("/{id}", name="booking_details")
     *
     * @Operation(description="Booking details by id", operationId="api_bookings_booking_details")
     * @OA\Response(response="200", description="Returns booking",
     *  @OA\JsonContent(
     *     type="object",
     *     ref=@Model(type=Booking::class, groups={"booking", "booking_schedule", "schedule_id", "schedule_name", "schedule_description", "schedule_booking_duration", "schedule_min_booking_time", "schedule_max_booking_time", "schedule_company", "company_id", "company_name"}),
     *     description="Booking"
     *  )
     * )
     * @OA\Response(response="404", description="Booking not found")
     * @Security(name="Bearer")
     *
     * @param string $id
     *
     * @return Response
     */
    public function booking(string $id): Response
    {
        $booking = $this->bookingManager->find($id);
        if (!$booking) {
            throw new EntryNotFoundException();
        }

        return $this->response($booking, Response::HTTP_OK, $this->serializeGroups);
    }
}
