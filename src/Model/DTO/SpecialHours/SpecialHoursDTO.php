<?php

namespace App\Model\DTO\SpecialHours;

use App\Entity\Schedule;
use App\Model\DTO\DTOInterface;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use DateTimeInterface;

class SpecialHoursDTO implements DTOInterface
{
    /**
     * @Assert\Type("App\Entity\Schedule", groups={"Default"})
     * @Assert\NotBlank(groups={"Default"})
     * @Serializer\Type("Relation<App\Entity\Schedule>")
     */
    protected ?Schedule $schedule = null;

    /**
     * @Assert\Type("string")
     * @Serializer\Type("string")
     */
    protected ?string $id = null;

    /**
     * @Assert\NotBlank(groups={"Default"})
     * @Serializer\Type("array<App\Model\DTO\SpecialHours\RangeDTO>")
     */
    protected ?array $ranges = null;

    /**
     * @Assert\Type("\DateTimeInterface", groups={"Default"})
     * @Serializer\Type("DateTime<'Y-m-d H:i:s'>")
     */
    protected ?DateTimeInterface $startDate;

    /**
     * @Assert\Type("\DateTimeInterface", groups={"Default"})
     * @Serializer\Type("DateTime<'Y-m-d H:i:s'>")
     */
    protected ?DateTimeInterface $endDate;

    /**
     * @Assert\Type("int", groups={"Default"})
     * @Serializer\Type("int")
     */
    protected ?int $repeatCondition;

    /**
     * @Assert\Type("int")
     * @Serializer\Type("int")
     */
    protected ?int $repeatDay;

    /**
     * @Assert\Type("\DateTimeInterface", groups={"Default"})
     * @Serializer\Type("DateTime<'Y-m-d H:i:s'>")
     */
    protected ?DateTimeInterface $repeatDate;

    /**
     * @Assert\Type("bool")
     * @Serializer\Type("bool")
     */
    protected ?bool $available;

    /**
     * SpecialHoursDTO constructor.
     * @param string|null $id
     * @param Schedule|null $schedule
     * @param array|null $ranges
     * @param DateTimeInterface|null $startDate
     * @param DateTimeInterface|null $endDate
     * @param int|null $repeatCondition
     * @param int|null $repeatDay
     * @param DateTimeInterface|null $repeatDate
     */
    public function __construct(
        ?string $id,
        ?Schedule $schedule,
        ?array $ranges,
        ?DateTimeInterface $startDate,
        ?DateTimeInterface $endDate,
        ?int $repeatCondition,
        ?int $repeatDay,
        ?DateTimeInterface $repeatDate,
        ?bool $available
    ) {
        $this->id = $id;
        $this->schedule = $schedule;
        $this->ranges = $ranges;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->repeatCondition = $repeatCondition;
        $this->repeatDay = $repeatDay;
        $this->repeatDate = $repeatDate;
        $this->available = $available;
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
     * @return array|null
     */
    public function getRanges(): ?array
    {
        return $this->ranges;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getStartDate(): ?DateTimeInterface
    {
        return $this->startDate;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getEndDate(): ?DateTimeInterface
    {
        return $this->endDate;
    }

    /**
     * @return int|null
     */
    public function getRepeatCondition(): ?int
    {
        return $this->repeatCondition;
    }

    /**
     * @return int|null
     */
    public function getRepeatDay(): ?int
    {
        return $this->repeatDay;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getRepeatDate(): ?DateTimeInterface
    {
        return $this->repeatDate;
    }

    /**
     * @return bool|null
     */
    public function getAvailable(): ?bool
    {
        return $this->available;
    }
}
