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

        return $this->response($company, Response::HTTP_OK, ['booking']);
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
     * @Rest\Get("/my", name="my_companies")
     * @return Response
     */
    public function myCompanies() : Response
    {
        return $this->response($this->getUser()->getFirstCompany() ?? [], Response::HTTP_OK, ['booking']);
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

    /**
     * @Rest\Get("/search/app", name="search")
     * @ParamConverter(
     *     "bookingFindDTO",
     *     converter="query_converter",
     *     options={"paramName"="filter"}
     * )
     * @param BookingFindDTO $bookingFindDTO
     * @return Response
     */
    public function search(BookingFindDTO $bookingFindDTO): Response
    {
        return $this->response(
            $this->bookingManager->findByDTO($bookingFindDTO),
            Response::HTTP_OK,
            ['booking_start', 'booking_end']
        );
    }
}
