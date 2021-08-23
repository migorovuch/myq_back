<?php

namespace App\Entity;

use App\Model\DTO\SpecialHours\RangeDTO;
use App\Model\Model\EntityInterface;
use App\Repository\SpecialHoursRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass=SpecialHoursRepository::class)
 */
class SpecialHours implements EntityInterface
{
    const REPEAT_EVERY_DAY = 0;
    const REPEAT_ONCE_A_WEAK = 1;
    const REPEAT_ONCE_A_MONTH = 2;
    const REPEAT_ONCE_A_YEAR = 3;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     * @Serializer\Groups({"special_hours", "special_hours_id"})
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity=Schedule::class, inversedBy="specialHours")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Serializer\Groups({"special_hours_schedule"})
     */
    protected $schedule;

    /**
     * @ORM\Column(type="json")
     * @Serializer\Groups({"special_hours", "special_hours_ranges"})
     */
    protected $ranges = [];

    /**
     * @ORM\Column(type="datetime")
     * @Serializer\Type("DateTime<'Y-m-d H:i:s'>")
     * @Serializer\Groups({"special_hours"})
     */
    protected $startDate;

    /**
     * @ORM\Column(type="datetime")
     * @Serializer\Type("DateTime<'Y-m-d H:i:s'>")
     * @Serializer\Groups({"special_hours"})
     */
    protected $endDate;

    /**
     * @ORM\Column(type="smallint")
     * @Serializer\Groups({"special_hours"})
     */
    protected $repeatCondition = self::REPEAT_ONCE_A_WEAK;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     * @Serializer\Groups({"special_hours"})
     */
    protected $repeatDay;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Serializer\Type("DateTime<'Y-m-d H:i:s'>")
     * @Serializer\Groups({"special_hours"})
     */
    protected $repeatDate;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Serializer\Groups({"special_hours"})
     */
    protected $available = true;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getSchedule(): ?Schedule
    {
        return $this->schedule;
    }

    public function setSchedule(?Schedule $schedule): self
    {
        $this->schedule = $schedule;

        return $this;
    }

    public function getRanges(): ?array
    {
        return $this->ranges;
    }

    public function setRanges(array $ranges): self
    {
        foreach ($ranges as &$range) {
            if ($range instanceof RangeDTO) {
                $range = $range->toArray();
            }
        }
        $this->ranges = $ranges;

        return $this;
    }

    public function getStartDate(): ?DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getRepeatCondition(): ?int
    {
        return $this->repeatCondition;
    }

    public function setRepeatCondition(int $repeatCondition): self
    {
        $this->repeatCondition = $repeatCondition;

        return $this;
    }

    public function getRepeatDay(): ?int
    {
        return $this->repeatDay;
    }

    public function setRepeatDay(?int $repeatDay): self
    {
        $this->repeatDay = $repeatDay;

        return $this;
    }

    public function getRepeatDate(): ?DateTimeInterface
    {
        return $this->repeatDate;
    }

    public function setRepeatDate(?DateTimeInterface $repeatDate): self
    {
        $this->repeatDate = $repeatDate;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAvailable(): bool
    {
        return $this->available;
    }

    /**
     * @param bool $available
     */
    public function setAvailable(bool $available): void
    {
        $this->available = $available;
    }
}
