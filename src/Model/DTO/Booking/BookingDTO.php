<?php

namespace App\Model\DTO\Booking;

use App\Entity\Booking;
use App\Entity\CompanyClient;
use App\Entity\Schedule;
use App\Model\DTO\DTOInterface;
use DateTime;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\ConstraintBookingAvailability;

/**
 * Class BookingDTO
 * @ConstraintBookingAvailability
 */
class BookingDTO implements DTOInterface
{

    /**
     * @Assert\Type("string", groups={"Default"})
     * @Serializer\Type("string")
     * @var string|null
     */
    protected ?string $id = null;

    /**
     * @Assert\Type("int", groups={"Default"})
     * @Assert\Choice(choices=App\Entity\Booking::STATUS_LIST, message="Wrong status selected", groups={"Default"})
     * @Serializer\Type("integer")
     * @var int|null
     */
    protected ?int $status = Booking::STATUS_NEW;

    /**
     * @Assert\Type("App\Entity\Schedule", groups={"Default"})
     * @Assert\NotBlank(groups={"Default"})
     * @Serializer\Type("Relation<App\Entity\Schedule>")
     */
    protected ?Schedule $schedule = null;

    /**
     * @Assert\Type("\DateTimeInterface", groups={"Default"})
     * @Serializer\Type("DateTime<'U'>")
     */
    protected ?DateTime $start = null;

    /**
     * @Assert\Type("\DateTimeInterface", groups={"Default"})
     * @Serializer\Type("DateTime<'U'>")
     */
    protected ?DateTime $end = null;

    /**
     * @Assert\Type("string")
     * @Serializer\Type("string")
     */
    protected ?string $title = null;

    /**
     * @Assert\Type("string")
     * @Serializer\Type("string")
     */
    protected ?string $customerComment = null;

    /**
     * @Assert\Type("App\Entity\CompanyClient", groups={"Default"})
     * @Serializer\Type("Relation<App\Entity\CompanyClient>")
     */
    protected ?CompanyClient $client = null;

    /**
     * @Assert\NotBlank(groups={"Default"})
     * @Assert\Type("string")
     * @Serializer\Type("string")
     */
    protected ?string $userName = null;

    /**
     * @Assert\NotBlank(groups={"Default"})
     * @Assert\Type("string", groups={"Default"})
     * @Serializer\Type("string")
     */
    protected ?string $userPhone = null;

    /**
     * @Assert\Type("boolean", groups={"Default"})
     * @Serializer\Type("boolean")
     */
    protected ?bool $newClient = false;

    /**
     * BookingDTO constructor.
     * @param string|null $id
     * @param int|null $status
     * @param Schedule|null $schedule
     * @param DateTime|null $filterFrom
     * @param DateTime|null $filterTo
     * @param string|null $title
     * @param string|null $customerComment
     * @param CompanyClient|null $client
     * @param string|null $userName
     * @param string|null $userPhone
     * @param bool|null $newClient
     */
    public function __construct(
        ?string $id = null,
        ?int $status = Booking::STATUS_NEW,
        ?Schedule $schedule = null,
        ?DateTime $filterFrom = null,
        ?DateTime $filterTo = null,
        ?string $title = null,
        ?string $customerComment = null,
        ?CompanyClient $client = null,
        ?string $userName = null,
        ?string $userPhone = null,
        ?bool $newClient = false
    ) {
        $this->id = $id;
        $this->schedule = $schedule;
        $this->start = $filterFrom;
        $this->end = $filterTo;
        $this->title = $title;
        $this->customerComment = $customerComment;
        $this->userName = $userName;
        $this->userPhone = $userPhone;
        $this->status = $status;
        $this->client = $client;
        $this->newClient = $newClient;
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return Schedule|null
     */
    public function getSchedule(): ?Schedule
    {
        return $this->schedule;
    }

    /**
     * @return int|null
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @return DateTime|null
     */
    public function getStart(): ?DateTime
    {
        return $this->start;
    }

    /**
     * @return DateTime|null
     */
    public function getEnd(): ?DateTime
    {
        return $this->end;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @return string|null
     */
    public function getCustomerComment(): ?string
    {
        return $this->customerComment;
    }

    /**
     * @return string|null
     */
    public function getUserName(): ?string
    {
        return $this->userName;
    }

    /**
     * @return string|null
     */
    public function getUserPhone(): ?string
    {
        return $this->userPhone;
    }

    /**
     * @return CompanyClient|null
     */
    public function getClient(): ?CompanyClient
    {
        return $this->client;
    }

    /**
     * @return bool
     */
    public function isNewClient(): bool
    {
        return $this->newClient;
    }
}
