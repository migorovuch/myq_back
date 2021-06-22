<?php

namespace App\Controller;

use App\Model\Manager\BookingManagerInterface;
use App\Model\DTO\Booking\BookingDTO;
use App\Model\DTO\Booking\BookingFindDTO;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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

    /**.
     * @param BookingManagerInterface $bookingManager
     */
    public function __construct(BookingManagerInterface $bookingManager)
    {
        $this->bookingManager = $bookingManager;
    }

    /**
     * @Rest\Post("/", name="create")
     * @ParamConverter("bookingDTO", converter="fos_rest.request_body", options={"deserializationContext"={"validationGroups"="Default"}})
     * @param BookingDTO $bookingDTO
     * @return Response
     */
    public function create(BookingDTO $bookingDTO): Response
    {
        $company = $this->bookingManager->create($bookingDTO);

        return $this->response(
            $company,
            Response::HTTP_OK,
            ['booking', 'booking_schedule', 'schedule_name', 'schedule_description']
        );
    }

    /**
     * @Rest\Put("/{id}", name="update")
     * @ParamConverter("bookingDTO", converter="fos_rest.request_body", options={"deserializationContext"={"validationGroups"="Default"}})
     * @param string $id
     * @param BookingDTO $bookingDTO
     * @return Response
     */
    public function update(string $id, BookingDTO $bookingDTO): Response
    {
        $company = $this->bookingManager->update($id, $bookingDTO);

        return $this->response($company, Response::HTTP_OK, ['booking']);
    }

    /**
     * @Rest\Get("/search", name="search")
     * @ParamConverter(
     *     "bookingFindDTO",
     *     converter="query_converter",
     *     options={"paramName"="filter", "deserializationContext"={"validationGroups"="Default"}}
     * )
     * @param BookingFindDTO $bookingFindDTO
     * @return Response
     */
    public function search(BookingFindDTO $bookingFindDTO): Response
    {
        return $this->response(
            $this->bookingManager->findByDTO($bookingFindDTO),
            Response::HTTP_OK,
            [
                'booking',
                'booking_schedule',
                'schedule_name',
                'schedule_description',
                'booking_user',
                'user_email',
                'user_nickname',
                'user_phone'
            ]
        );
    }

    /**
     * @Rest\Get("/my", name="my")
     * @ParamConverter(
     *     "bookingFindDTO",
     *     converter="query_converter",
     *     options={"paramName"="filter", "deserializationContext"={"validationGroups"="Default"}}
     * )
     * @param BookingFindDTO $bookingFindDTO
     * @return Response
     */
    public function myBookings(BookingFindDTO $bookingFindDTO) : Response
    {
        $bookingFindDTO = new BookingFindDTO(
            $bookingFindDTO->getId(),
            $bookingFindDTO->getStatus(),
            $bookingFindDTO->getSchedule(),
            $bookingFindDTO->getCompany(),
            $bookingFindDTO->getFilterFrom(),
            $bookingFindDTO->getFilterTo(),
            $bookingFindDTO->getTitle(),
            $bookingFindDTO->getCustomerComment(),
            $this->getUser(),
            $bookingFindDTO->getUserName(),
            $bookingFindDTO->getUserPhone(),
            $bookingFindDTO->getSort(),
            $bookingFindDTO->getPage(),
            $bookingFindDTO->getCondition()
        );
        return $this->response(
            $this->bookingManager->findByDTO($bookingFindDTO),
            Response::HTTP_OK,
            ['booking', 'booking_schedule', 'schedule_name', 'schedule_description']
        );
    }

    /**
     * @Rest\Get ("/{id}", name="booking")
     * @param string $id
     * @return Response
     */
    public function booking(string $id) : Response
    {
        $company = $this->bookingManager->find($id);
        if (!$company) {
            throw new NotFoundHttpException();
        }

        return $this->response($company, Response::HTTP_OK, ['booking']);
    }
}
