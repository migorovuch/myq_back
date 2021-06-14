<?php

namespace App\Model\DTO\Booking;

use App\Entity\Schedule;
use App\Entity\User;
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
     * @Assert\Type("App\Entity\User", groups={"Default"})
     * @Serializer\Type("Relation<App\Entity\User>")
     */
    protected ?User $user = null;

    /**
     * @Assert\Type("string")
     * @Serializer\Type("string")
     */
    protected ?string $userName = null;

    /**
     * @Assert\Type("string")
     * @Serializer\Type("string")
     */
    protected ?string $userPhone = null;

    /**
     * BookingDTO constructor.
     * @param string|null $id
     * @param Schedule|null $schedule
     * @param DateTime|null $filterFrom
     * @param DateTime|null $filterTo
     * @param string|null $title
     * @param string|null $customerComment
     * @param User|null $user
     * @param string|null $userName
     * @param string|null $userPhone
     */
    public function __construct(
        ?string $id = null,
        ?Schedule $schedule = null,
        ?DateTime $filterFrom = null,
        ?DateTime $filterTo = null,
        ?string $title = null,
        ?string $customerComment = null,
        ?User $user = null,
        ?string $userName = null,
        ?string $userPhone = null
    ) {
        $this->id = $id;
        $this->schedule = $schedule;
        $this->start = $filterFrom;
        $this->end = $filterTo;
        $this->title = $title;
        $this->customerComment = $customerComment;
        $this->user = $user;
        $this->userName = $userName;
        $this->userPhone = $userPhone;
    }

    /**
     * @return Schedule|null
     */
    public function getSchedule(): ?Schedule
    {
        return $this->schedule;
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
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
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
}
