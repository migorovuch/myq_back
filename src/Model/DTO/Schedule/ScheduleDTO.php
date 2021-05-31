<?php

namespace App\Model\DTO\Schedule;

use App\Model\DTO\DTOInterface;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class ScheduleDTO implements DTOInterface
{
    /**
     * @Assert\Type("string")
     * @Serializer\Type("string")
     */
    protected ?string $name;

    /**
     * @Assert\Type("boolean")
     * @Serializer\Type("boolean")
     */
    protected ?string $enabled;

    /**
     * @Assert\Type("boolean")
     * @Serializer\Type("boolean")
     */
    protected ?bool $available;

    /**
     * @Assert\Type("integer")
     * @Serializer\Type("integer")
     */
    protected ?int $bookingDuration;

    /**
     * @Assert\Type("integer")
     * @Assert\LessThan("minBookingTime")
     * @Serializer\Type("integer")
     */
    protected ?int $minBookingTime;

    /**
     * @Assert\Type("integer")
     * @Assert\GreaterThan("minBookingTime")
     * @Serializer\Type("integer")
     */
    protected ?int $maxBookingTime;

    /**
     * @Assert\Type("string")
     * @Serializer\Type("string")
     */
    protected ?string $description;

    /**
     * @Assert\Type("integer")
     * @Serializer\Type("integer")
     */
    protected ?int $bookingCondition;

    /**
     * @Assert\Type("integer")
     * @Serializer\Type("integer")
     */
    protected ?int $acceptBookingCondition;

    /**
     * ScheduleDTO constructor.
     * @param string|null $name
     * @param string|null $enabled
     * @param bool|null $available
     * @param int|null $bookingDuration
     * @param int|null $minBookingTime
     * @param int|null $maxBookingTime
     * @param string|null $description
     * @param int|null $bookingCondition
     * @param int|null $acceptBookingCondition
     */
    public function __construct(
        ?string $name,
        ?string $enabled,
        ?bool $available,
        ?int $bookingDuration,
        ?int $minBookingTime,
        ?int $maxBookingTime,
        ?string $description,
        ?int $bookingCondition,
        ?int $acceptBookingCondition
    ) {
        $this->name = $name;
        $this->enabled = $enabled;
        $this->available = $available;
        $this->bookingDuration = $bookingDuration;
        $this->minBookingTime = $minBookingTime;
        $this->maxBookingTime = $maxBookingTime;
        $this->description = $description;
        $this->bookingCondition = $bookingCondition;
        $this->acceptBookingCondition = $acceptBookingCondition;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getEnabled(): ?string
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
}
