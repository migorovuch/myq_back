<?php

namespace App\Entity;

use App\Model\Model\EntityInterface;
use App\Repository\ScheduleRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass=ScheduleRepository::class)
 */
class Schedule implements EntityInterface
{

    const ACCEPT_BOOKING_DO_NOTHING = 0;
    const ACCEPT_BOOKING_ACCEPT_ALL = 1;
    const ACCEPT_BOOKING_DECLINE_ALL = 2;
    const ACCEPT_BOOKING_ACCEPT_APPROVED_USERS = 3;
    const ACCEPT_BOOKING_ACCEPT_AFTER_PAY_ADVANCE = 4;

    const BOOKING_CONDITION_ALL_USERS = 0;
    const BOOKING_CONDITION_AUTHORIZED_USERS = 1;

    const DEFAULT_BOOKING_DURATION = 30;
    const DEFAULT_ACCEPT_BOOKING_TIME = 60;
    const DEFAULT_TIME_BETWEEN_BOOKINGS = 0;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     * @Serializer\Groups({"schedule", "schedule_id"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="schedules")
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\Groups({"schedule_company"})
     */
    private $company;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups({"schedule", "schedule_name"})
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     * @Serializer\Groups({"schedule"})
     * available for booking
     */
    private $enabled = true;

    /**
     * @ORM\Column(type="boolean")
     * @Serializer\Groups({"schedule"})
     * Always available for booking or only in specialHours
     */
    private $available = false;

    /**
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"schedule", "schedule_booking_duration"})
     * Minutes, 0 - manual setting for each booking
     */
    private $bookingDuration = self::DEFAULT_BOOKING_DURATION;

    /**
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"schedule", "schedule_min_booking_time"})
     * Minutes, available only in case bookingDuration==0
     */
    private $minBookingTime = 0;

    /**
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"schedule", "schedule_max_booking_time"})
     * Minutes, available only in case bookingDuration==0, 0 - no limit
     */
    private $maxBookingTime = 0;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Serializer\Groups({"schedule", "schedule_description"})
     */
    private $description;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     * @Serializer\Groups({"schedule"})
     * Who can book - all users or authenticated only
     */
    private $bookingCondition = self::BOOKING_CONDITION_ALL_USERS;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     * @Serializer\Groups({"schedule"})
     */
    private $acceptBookingCondition = self::ACCEPT_BOOKING_DO_NOTHING;

    /**
     * Time from now to booking
     * @ORM\Column(type="integer", nullable=true)
     * @Serializer\Groups({"schedule"})
     */
    private $acceptBookingTime = self::DEFAULT_ACCEPT_BOOKING_TIME;

    /**
     * Time between bookings
     * @ORM\Column(type="integer", nullable=true)
     * @Serializer\Groups({"schedule"})
     */
    private $timeBetweenBookings = self::DEFAULT_TIME_BETWEEN_BOOKINGS;

    /**
     * @ORM\OneToMany(targetEntity=SpecialHours::class, mappedBy="schedule", orphanRemoval=true)
     * @Serializer\Groups({"schedule_special_hours"})
     */
    private $specialHours;

    /**
     * @ORM\OneToMany(targetEntity=Booking::class, mappedBy="schedule")
     * @Serializer\Groups({"schedule_bookings"})
     */
    private $bookings;

    /**
     * @ORM\Column(type="datetime")
     * @Serializer\Groups({"booking_created"})
     */
    protected ?DateTime $createdAt = null;

    /**
     * @ORM\Column(type="datetime")
     * @Serializer\Groups({"booking_updated"})
     */
    protected ?DateTime $updatedAt = null;

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
     * @return int
     */
    public function getAcceptBookingTime(): int
    {
        return $this->acceptBookingTime;
    }

    /**
     * @param int $acceptBookingTime
     */
    public function setAcceptBookingTime(int $acceptBookingTime): self
    {
        $this->acceptBookingTime = $acceptBookingTime;

        return $this;
    }

    /**
     * @param int $timeBetweenBookings
     */
    public function setTimeBetweenBookings(int $timeBetweenBookings): self
    {
        $this->timeBetweenBookings = $timeBetweenBookings;

        return $this;
    }

    /**
     * @return int
     */
    public function getTimeBetweenBookings(): int
    {
        return $this->timeBetweenBookings;
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

    /**
     * @return DateTime|null
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime|null $createdAt
     * @return Schedule
     */
    public function setCreatedAt(?DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime|null $updatedAt
     * @return Schedule
     */
    public function setUpdatedAt(?DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps(): void
    {
        $this->setUpdatedAt(new DateTime('now'));
        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt(new DateTime('now'));
        }
    }
}
