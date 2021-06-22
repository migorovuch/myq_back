<?php

namespace App\Model\DTO\Availability;

use App\Model\DTO\AbstractFindDTO;
use App\Model\DTO\PageDTO;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use DateTimeInterface;
use App\Entity\Schedule;

class AvailabilityFindDTO extends AbstractFindDTO
{

    /**
     * @Assert\Type("string", groups={"Default"})
     * @Assert\NotBlank(groups={"Default"})
     * @Serializer\Type("Relation<App\Entity\Schedule>")
     */
    protected ?Schedule $schedule = null;

    /**
     * @Assert\Type("\DateTimeInterface", groups={"Default"})
     * @Assert\NotBlank(groups={"Default"})
     * @Serializer\Type("DateTime<'U'>")
     */
    protected ?DateTimeInterface $filterFrom = null;

    /**
     * @Assert\Type("\DateTimeInterface", groups={"Default"})
     * @Assert\NotBlank(groups={"Default"})
     * @Serializer\Type("DateTime<'U'>")
     */
    protected ?DateTimeInterface $filterTo = null;

    /**
     * AvailabilityFindDTO constructor.
     * @param Schedule|null $schedule
     * @param DateTimeInterface|null $filterFrom
     * @param DateTimeInterface|null $filterTo
     */
    public function __construct(
        ?Schedule $schedule = null,
        ?DateTimeInterface $filterFrom = null,
        ?DateTimeInterface $filterTo = null,
        array $sort = null,
        PageDTO $page = null,
        string $condition = self::CONDITION_AND
    ) {
        parent::__construct($sort, $page, $condition);
        $this->schedule = $schedule;
        $this->filterFrom = $filterFrom;
        $this->filterTo = $filterTo;
    }

    /**
     * @return Schedule|null
     */
    public function getSchedule(): ?Schedule
    {
        return $this->schedule;
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

}
