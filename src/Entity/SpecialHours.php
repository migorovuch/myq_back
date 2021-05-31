<?php

namespace App\Entity;

use App\Repository\SpecialHoursRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SpecialHoursRepository::class)
 */
class SpecialHours
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity=Schedule::class, inversedBy="specialHours")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $schedule;

    /**
     * @ORM\Column(type="json")
     */
    protected $ranges = [];

    /**
     * @ORM\Column(type="datetime")
     */
    protected $startDate;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $endDate;

    /**
     * @ORM\Column(type="smallint")
     */
    protected $repeatCondition;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    protected $repeatDay;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $repeatDate;

    /**
     * @ORM\Column(type="boolean", nullable=true)
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
        $this->ranges = $ranges;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
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

    public function getRepeatDate(): ?\DateTimeInterface
    {
        return $this->repeatDate;
    }

    public function setRepeatDate(?\DateTimeInterface $repeatDate): self
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
