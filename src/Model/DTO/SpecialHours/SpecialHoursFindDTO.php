<?php

namespace App\Model\DTO\SpecialHours;

use App\Entity\Schedule;
use App\Model\DTO\AbstractFindDTO;
use App\Model\DTO\PageDTO;
use DateTimeInterface;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class SpecialHoursFindDTO extends AbstractFindDTO
{
    /**
     * @Assert\Type("string", groups={"Default"})
     * @Serializer\Type("string")
     */
    protected ?string $id = null;

    /**
     * @Assert\Type("App\Entity\Schedule", groups={"Default"})
     * @Serializer\Type("Relation<App\Entity\Schedule>")
     */
    protected ?Schedule $schedule = null;

    /**
     * @Assert\Type("int", groups={"Default"})
     * @Serializer\Type("int")
     */
    protected ?int $repeatCondition = null;

    /**
     * @Assert\Type("int", groups={"Default"})
     * @Serializer\Type("int")
     */
    protected ?int $repeatDay = null;

    /**
     * @Assert\Type("\DateTimeInterface", groups={"Default"})
     * @Serializer\Type("DateTimeInterface<'Y-m-d H:i:s'>")
     */
    protected ?DateTimeInterface $filterRepeatDate = null;

    /**
     * @Assert\Type("\DateTimeInterface", groups={"Default"})
     * @Serializer\Type("DateTimeInterface<'Y-m-d H:i:s'>")
     */
    protected ?DateTimeInterface $filterFrom = null;

    /**
     * @Assert\Type("\DateTimeInterface", groups={"Default"})
     * @Serializer\Type("DateTimeInterface<'Y-m-d H:i:s'>")
     */
    protected ?DateTimeInterface $filterTo = null;

    /**
     * @Assert\Type("boolean", groups={"Default"})
     * @Serializer\Type("boolean")
     */
    protected ?bool $available = null;

    /**
     * SpecialHoursFindDTO constructor.
     *
     * @param string|null            $id
     * @param Schedule|null          $schedule
     * @param int|null               $repeatCondition
     * @param int|null               $repeatDay
     * @param DateTimeInterface|null $repeatDate
     * @param DateTimeInterface|null $filterFrom
     * @param DateTimeInterface|null $filterTo
     * @param bool|null              $available
     * @param array|null             $sort
     * @param PageDTO|null           $page
     * @param string                 $condition
     */
    public function __construct(
        ?string $id = null,
        ?Schedule $schedule = null,
        ?int $repeatCondition = null,
        ?int $repeatDay = null,
        ?DateTimeInterface $repeatDate = null,
        ?DateTimeInterface $filterFrom = null,
        ?DateTimeInterface $filterTo = null,
        ?bool $available = null,
        ?array $sort = null,
        ?PageDTO $page = null,
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
        $this->id = $id;
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
    public function getFilterRepeatDate(): ?DateTimeInterface
    {
        return $this->filterRepeatDate;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getFilterFrom(): ?DateTimeInterface
    {
        return $this->filterFrom;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getFilterTo(): ?DateTimeInterface
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
