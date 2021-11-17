<?php

namespace App\Model\DTO\Booking;

use App\Entity\Booking;
use App\Entity\Schedule;
use App\Model\DTO\DTOInterface;
use App\Validator\ConstraintBookingAvailability;
use DateTime;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ChangeBookingDTO.
 *
 * @ConstraintBookingAvailability
 */
class ChangeBookingDTO implements DTOInterface, BookingAvailabilityDTOInterface
{
    /**
     * @Assert\Type("string", groups={"Default"})
     * @Serializer\Type("string")
     *
     * @var string|null
     */
    protected ?string $id = null;

    /**
     * @Assert\Type("int", groups={"Default"})
     * @Assert\Choice(choices=App\Entity\Booking::STATUS_LIST, message="Wrong status selected", groups={"Default"})
     * @Serializer\Type("integer")
     *
     * @var int|null
     */
    protected ?int $status = Booking::STATUS_NEW;

    /**
     * @Assert\Type("App\Entity\Schedule", groups={"Default"})
     * @Assert\NotBlank(groups={"Default"}, message="This value should not be blank")
     * @Serializer\Type("Relation<App\Entity\Schedule>")
     * @Serializer\Groups({"booking_schedule"})
     */
    protected ?Schedule $schedule = null;

    /**
     * @Assert\Type("\DateTimeInterface", groups={"Default"})
     * @Serializer\Type("DateTime<'U'>")
     * @Serializer\Groups({"booking", "booking_start"})
     */
    protected ?DateTime $start = null;

    /**
     * @Assert\Type("\DateTimeInterface", groups={"Default"})
     * @Serializer\Type("DateTime<'U'>")
     * @Serializer\Groups({"booking", "booking_end"})
     */
    protected ?DateTime $end = null;

    /**
     * @Assert\Type("string", groups={"Default"})
     * @Serializer\Type("string")
     * @Serializer\Groups({"booking_title"})
     */
    protected ?string $title = null;

    /**
     * @Assert\Type("boolean", groups={"Default"})
     * @Serializer\Type("boolean")
     * @Serializer\Groups({"booking_client_name"})
     */
    protected ?bool $newClient = false;

    /**
     * ChangeBookingDTO constructor.
     *
     * @param string|null   $id
     * @param int|null      $status
     * @param Schedule|null $schedule
     * @param DateTime|null $filterFrom
     * @param DateTime|null $filterTo
     * @param string|null   $title
     * @param bool|null     $newClient
     */
    public function __construct(
        ?string $id = null,
        ?int $status = Booking::STATUS_NEW,
        ?Schedule $schedule = null,
        ?DateTime $filterFrom = null,
        ?DateTime $filterTo = null,
        ?string $title = null,
        ?bool $newClient = false
    ) {
        $this->id = $id;
        $this->schedule = $schedule;
        $this->start = $filterFrom;
        $this->end = $filterTo;
        $this->title = $title;
        $this->status = $status;
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
     * @return bool
     */
    public function isNewClient(): bool
    {
        return $this->newClient;
    }
}
