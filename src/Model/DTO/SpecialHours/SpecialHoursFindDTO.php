<?php

namespace App\Model\DTO\SpecialHours;

use App\Entity\Schedule;
use App\Model\DTO\AbstractFindDTO;
use App\Model\DTO\PageDTO;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use DateTime;

class SpecialHoursFindDTO extends AbstractFindDTO
{
    /**
     * @Assert\Type("string")
     * @Serializer\Type("App\Entity\Schedule")
     */
    protected ?Schedule $schedule;

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
    protected ?DateTime $filterRepeatDate;

    /**
     * @Assert\Type("datetime")
     * @Serializer\Type("datetime")
     */
    protected ?DateTime $filterFrom;

    /**
     * @Assert\Type("datetime")
     * @Serializer\Type("datetime")
     */
    protected ?DateTime $filterTo;

    /**
     * @Assert\Type("bool")
     * @Serializer\Type("bool")
     */
    protected ?bool $available;

    /**
     * SpecialHoursFindDTO constructor.
     * @param Schedule|null $schedule
     * @param int|null $repeatCondition
     * @param int|null $repeatDay
     * @param DateTime|null $repeatDate
     * @param DateTime|null $filterFrom
     * @param DateTime|null $filterTo
     */
    public function __construct(
        ?Schedule $schedule,
        ?int $repeatCondition,
        ?int $repeatDay,
        ?DateTime $repeatDate,
        ?DateTime $filterFrom,
        ?DateTime $filterTo,
        ?bool $available,
        ?string $sort,
        ?PageDTO $page,
        string $condition = self::CONDITION_AND
    ) {
        parent::__construct($sort, $page, $condition);
        $this->schedule = $schedule;
        $this->repeatCondition = $repeatCondition;
        $this->repeatDay = $repeatDay;
        $this->filterRepeatDate = $repeatDate;
        $this->filterFrom = $filterFrom;
        $this->filterTo = $filterTo;
        $this->available = $available;
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
    public function getFilterRepeatDate(): ?DateTime
    {
        return $this->filterRepeatDate;
    }

    /**
     * @return DateTime|null
     */
    public function getFilterFrom(): ?DateTime
    {
        return $this->filterFrom;
    }

    /**
     * @return DateTime|null
     */
    public function getFilterTo(): ?DateTime
    {
        return $this->filterTo;
    }

    /**
     * @return bool|null
     */
    public function getAvailable(): ?bool
    {
        return $this->available;
    }

}
