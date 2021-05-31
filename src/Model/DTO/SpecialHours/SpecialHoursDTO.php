<?php

namespace App\Model\DTO\SpecialHours;

use App\Entity\Schedule;
use App\Model\DTO\DTOInterface;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use DateTime;

class SpecialHoursDTO implements DTOInterface
{
    /**
     * @Assert\Type("string")
     * @Serializer\Type("App\Entity\Schedule")
     */
    protected ?Schedule $schedule;

    /**
     * @Assert\Type("string")
     * @Serializer\Type("string")
     */
    protected ?string $id;

    /**
     * @Assert\Json()
     * @Serializer\Type("string")
     */
    protected ?array $ranges = [];

    /**
     * @Assert\Type("datetime")
     * @Serializer\Type("datetime")
     */
    protected ?DateTime $startDate;

    /**
     * @Assert\Type("datetime")
     * @Serializer\Type("datetime")
     */
    protected ?DateTime $endDate;

    /**
     * @Assert\Type("smallint")
     * @Serializer\Type("smallint")
     */
    protected ?int $repeatCondition;

    /**
     * @Assert\Type("smallint")
     * @Serializer\Type("smallint")
     */
    protected ?int $repeatDay;

    /**
     * @Assert\Type("datetime")
     * @Serializer\Type("datetime")
     */
    protected ?DateTime $repeatDate;

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
     * @param DateTime|null $startDate
     * @param DateTime|null $endDate
     * @param int|null $repeatCondition
     * @param int|null $repeatDay
     * @param DateTime|null $repeatDate
     */
    public function __construct(
        ?string $id,
        ?Schedule $schedule,
        ?array $ranges,
        ?DateTime $startDate,
        ?DateTime $endDate,
        ?int $repeatCondition,
        ?int $repeatDay,
        ?DateTime $repeatDate,
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
     * @return DateTime|null
     */
    public function getStartDate(): ?DateTime
    {
        return $this->startDate;
    }

    /**
     * @return DateTime|null
     */
    public function getEndDate(): ?DateTime
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
     * @return DateTime|null
     */
    public function getRepeatDate(): ?DateTime
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
