<?php

namespace App\Entity;

use App\Repository\ScheduleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ScheduleRepository::class)
 */
class Schedule
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="schedules")
     * @ORM\JoinColumn(nullable=false)
     */
    private $company;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $enabled;

    /**
     * @ORM\Column(type="boolean")
     */
    private $available;

    /**
     * @ORM\Column(type="integer")
     */
    private $bookingDuration;

    /**
     * @ORM\Column(type="integer")
     */
    private $minBookingTime;

    /**
     * @ORM\Column(type="integer")
     */
    private $maxBookingTime;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $bookingCondition;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $acceptBookingCondition;

    /**
     * @ORM\OneToMany(targetEntity=SpecialHours::class, mappedBy="schedule", orphanRemoval=true)
     */
    private $specialHours;

    /**
     * @ORM\OneToMany(targetEntity=Booking::class, mappedBy="schedule")
     */
    private $bookings;

    public function __construct()
    {
        $this->specialHours = new ArrayCollection();
        $this->bookings = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getAvailable(): ?bool
    {
        return $this->available;
    }

    public function setAvailable(bool $available): self
    {
        $this->available = $available;

        return $this;
    }

    public function getBookingDuration(): ?int
    {
        return $this->bookingDuration;
    }

    public function setBookingDuration(int $bookingDuration): self
    {
        $this->bookingDuration = $bookingDuration;

        return $this;
    }

    public function getMinBookingTime(): ?int
    {
        return $this->minBookingTime;
    }

    public function setMinBookingTime(int $minBookingTime): self
    {
        $this->minBookingTime = $minBookingTime;

        return $this;
    }

    public function getMaxBookingTime(): ?int
    {
        return $this->maxBookingTime;
    }

    public function setMaxBookingTime(int $maxBookingTime): self
    {
        $this->maxBookingTime = $maxBookingTime;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getBookingCondition(): ?int
    {
        return $this->bookingCondition;
    }

    public function setBookingCondition(?int $bookingCondition): self
    {
        $this->bookingCondition = $bookingCondition;

        return $this;
    }

    public function getAcceptBookingCondition(): ?int
    {
        return $this->acceptBookingCondition;
    }

    public function setAcceptBookingCondition(?int $acceptBookingCondition): self
    {
        $this->acceptBookingCondition = $acceptBookingCondition;

        return $this;
    }

    /**
     * @return Collection|SpecialHours[]
     */
    public function getSpecialHours(): Collection
    {
        return $this->specialHours;
    }

    public function addSpecialHour(SpecialHours $specialHour): self
    {
        if (!$this->specialHours->contains($specialHour)) {
            $this->specialHours[] = $specialHour;
            $specialHour->setSchedule($this);
        }

        return $this;
    }

    public function removeSpecialHour(SpecialHours $specialHour): self
    {
        if ($this->specialHours->removeElement($specialHour)) {
            // set the owning side to null (unless already changed)
            if ($specialHour->getSchedule() === $this) {
                $specialHour->setSchedule(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Booking[]
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): self
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings[] = $booking;
            $booking->setSchedule($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): self
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getSchedule() === $this) {
                $booking->setSchedule(null);
            }
        }

        return $this;
    }
}
