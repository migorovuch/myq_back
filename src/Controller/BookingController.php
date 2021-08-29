<?php

namespace App\Controller;

use App\Model\DTO\Booking\BookingDTO;
use App\Model\DTO\Booking\BookingFindDTO;
use App\Model\DTO\Booking\ChangeBookingDTO;
use App\Model\Manager\BookingManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BookingController.
 *
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
            'booking_client',
            'booking_title',
            'company_client',
            'schedule_company',
            'company_id',
            'company_name',
            'company_client_pseudonym',
        ];
    }

    /**
     * @Rest\Post("/", name="create")
     * @ParamConverter("bookingDTO", converter="fos_rest.request_body", options={"deserializationContext"={"validationGroups"="Default"}})
     *
     * @param BookingDTO $bookingDTO
     *
     * @return Response
     */
    public function create(BookingDTO $bookingDTO): Response
    {
        $booking = $this->bookingManager->create($bookingDTO);

        return $this->response(
            $booking,
            Response::HTTP_OK,
            $this->serializeGroups
        );
    }

    /**
     * @Rest\Put("/{id}", name="update")
     * @ParamConverter("bookingDTO", converter="fos_rest.request_body", options={"deserializationContext"={"validationGroups"="Default"}})
     *
     * @param string     $id
     * @param BookingDTO $bookingDTO
     *
     * @return Response
     */
    public function update(string $id, BookingDTO $bookingDTO): Response
    {
        $company = $this->bookingManager->update($id, $bookingDTO);

        return $this->response($company, Response::HTTP_OK, $this->serializeGroups);
    }

    /**
     * @Rest\Patch("/{id}", name="change")
     * @ParamConverter("changeBookingDTO", converter="fos_rest.request_body", options={"deserializationContext"={"validationGroups"="Default"}})
     *
     * @param string           $id
     * @param ChangeBookingDTO $changeBookingDTO
     *
     * @return Response
     */
    public function change(string $id, ChangeBookingDTO $changeBookingDTO): Response
    {
        $company = $this->bookingManager->change($id, $changeBookingDTO);

        return $this->response($company, Response::HTTP_OK, $this->serializeGroups);
    }

    /**
     * @Rest\Get("/search", name="search")
     * @ParamConverter(
     *     "bookingFindDTO",
     *     converter="query_converter",
     *     options={"paramName"="filter", "validationGroups"="Default", "validationGroupsRole"={"ROLE_USER"="booking_company"}}
     * )
     *
     * @param BookingFindDTO $bookingFindDTO
     *
     * @return Response
     */
    public function search(BookingFindDTO $bookingFindDTO): Response
    {
        $data = $this->bookingManager->findByDTO($bookingFindDTO);
        $total = $this->bookingManager->countByDTO($bookingFindDTO);

        return $this->response(
            [
                'data' => $data,
                'total' => $total,
            ],
            Response::HTTP_OK,
            $this->serializeGroups
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
     * @param BookingFindDTO $bookingFindDTO
     *
     * @return Response
     */
    public function myBookings(BookingFindDTO $bookingFindDTO): Response
    {
        $bookingFindDTO = $this->bookingManager->buildMyBookingFindDTO($bookingFindDTO);
        $result = $this->bookingManager->findByDTO($bookingFindDTO);
        $total = $this->bookingManager->countByDTO($bookingFindDTO);

        return $this->response(
            [
                'data' => $result,
                'total' => $total,
            ],
            Response::HTTP_OK,
            [
                'booking',
                'booking_schedule',
                'schedule_id',
                'schedule_name',
                'schedule_description',
                'schedule_booking_duration',
                'schedule_min_booking_time',
                'schedule_max_booking_time',
                'booking_client',
                'booking_title',
                'company_client',
                'schedule_company',
                'company_id',
                'company_name',
            ]
        );
    }

    /**
     * @Rest\Get ("/{id}", name="booking")
     *
     * @param string $id
     *
     * @return Response
     */
    public function booking(string $id): Response
    {
        $company = $this->bookingManager->find($id);
        if (!$company) {
            throw new NotFoundHttpException();
        }

        return $this->response($company, Response::HTTP_OK, $this->serializeGroups);
    }
}
