<?php

namespace App\Model\DTO\Schedule;

use App\Entity\Company;
use App\Entity\Schedule;
use App\Model\DTO\DTOInterface;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class ScheduleDTO implements DTOInterface
{
    /**
     * @Assert\Type("App\Entity\Company", groups={"Default"})
     * @Assert\NotBlank(groups={"Default"}, message="This value should not be blank")
     * @Serializer\Type("Relation<App\Entity\Company>")
     */
    protected ?Company $company = null;

    /**
     * @Assert\NotBlank(groups={"Default"}, message="This value should not be blank")
     * @Assert\Type("string", groups={"Default"})
     * @Serializer\Type("string")
     */
    protected ?string $name = null;

    /**
     * @Assert\Type("boolean")
     * @Serializer\Type("boolean")
     */
    protected ?bool $enabled = false;

    /**
     * @Assert\Type("boolean")
     * @Serializer\Type("boolean")
     */
    protected ?bool $available = false;

    /**
     * @Assert\Type("integer")
     * @Serializer\Type("integer")
     */
    protected ?int $bookingDuration = Schedule::DEFAULT_BOOKING_DURATION;

    /**
     * @Assert\Type("integer")
     * @Assert\LessThan("maxBookingTime", groups={"Default"}, message="This value should be lower than Max. booking time")
     * @Serializer\Type("integer")
     */
    protected ?int $minBookingTime = 0;

    /**
     * @Assert\Type("integer", groups={"Default"})
     * @Assert\GreaterThanOrEqual(propertyPath="minBookingTime", groups={"Default"}, message="This value should be greater or equal than Min. booking time")
     * @Serializer\Type("integer")
     */
    protected ?int $maxBookingTime = 0;

    /**
     * @Assert\Type("string", groups={"Default"})
     * @Serializer\Type("string")
     */
    protected ?string $description = null;

    /**
     * @Assert\Type("integer")
     * @Serializer\Type("integer")
     */
    protected ?int $bookingCondition = null;

    /**
     * @Assert\Type("integer")
     * @Serializer\Type("integer")
     */
    protected ?int $acceptBookingCondition = null;

    /**
     * @Assert\Type("integer")
     * @Assert\PositiveOrZero
     * @Serializer\Type("integer")
     */
    protected ?int $acceptBookingTime = Schedule::DEFAULT_ACCEPT_BOOKING_TIME;

    /**
     * Time between bookings
     * @Assert\Type("integer")
     * @Serializer\Type("integer")
     */
    protected ?int $timeBetweenBookings = Schedule::DEFAULT_TIME_BETWEEN_BOOKINGS;

    /**
     * ScheduleDTO constructor.
     * @param Company|null $company
     * @param string|null $name
     * @param bool|null $enabled
     * @param bool|null $available
     * @param int|null $bookingDuration
     * @param int|null $minBookingTime
     * @param int|null $maxBookingTime
     * @param string|null $description
     * @param int|null $bookingCondition
     * @param int|null $acceptBookingCondition
     * @param int|null $acceptBookingTime
     * @param int|null $timeBetweenBookings
     */
    public function __construct(
        ?Company $company = null,
        ?string $name = null,
        ?bool $enabled = null,
        ?bool $available = null,
        ?int $bookingDuration = null,
        ?int $minBookingTime = null,
        ?int $maxBookingTime = null,
        ?string $description = null,
        ?int $bookingCondition = null,
        ?int $acceptBookingCondition = null,
        ?int $acceptBookingTime = null,
        ?int $timeBetweenBookings = null
    ) {
        $this->company = $company;
        $this->name = $name;
        $this->enabled = $enabled;
        $this->available = $available;
        $this->bookingDuration = $bookingDuration;
        $this->minBookingTime = $minBookingTime;
        $this->maxBookingTime = $maxBookingTime;
        $this->description = $description;
        $this->bookingCondition = $bookingCondition;
        $this->acceptBookingCondition = $acceptBookingCondition;
        $this->acceptBookingTime = $acceptBookingTime;
        $this->timeBetweenBookings = $timeBetweenBookings;
    }

    /**
     * @return Company|null
     */
    public function getCompany(): ?Company
    {
        return $this->company;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return bool|null
     */
    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    /**
     * @return bool|null
     */
    public function getAvailable(): ?bool
    {
        return $this->available;
    }

    /**
     * @return int|null
     */
    public function getBookingDuration(): ?int
    {
        return $this->bookingDuration;
    }

    /**
     * @return int|null
     */
    public function getMinBookingTime(): ?int
    {
        return $this->minBookingTime;
    }

    /**
     * @return int|null
     */
    public function getMaxBookingTime(): ?int
    {
        return $this->maxBookingTime;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return int|null
     */
    public function getBookingCondition(): ?int
    {
        return $this->bookingCondition;
    }

    /**
     * @return int|null
     */
    public function getAcceptBookingCondition(): ?int
    {
        return $this->acceptBookingCondition;
    }

    /**
     * @return int|null
     */
    public function getAcceptBookingTime(): ?int
    {
        return $this->acceptBookingTime;
    }

    /**
     * @return int|null
     */
    public function getTimeBetweenBookings(): ?int
    {
        return $this->timeBetweenBookings;
    }
}
