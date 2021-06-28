<?php

namespace App\Entity;

use App\Model\Model\EntityInterface;
use App\Repository\BookingRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass=BookingRepository::class)
 */
class Booking implements EntityInterface
{

    const STATUS_NEW = 0;
    const STATUS_ACCEPTED = 1;
    const STATUS_DECLINED = 2;

    const STATUS_LIST = [
        self::STATUS_NEW => self::STATUS_NEW,
        self::STATUS_ACCEPTED => self::STATUS_ACCEPTED,
        self::STATUS_DECLINED => self::STATUS_DECLINED,
    ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     * @Serializer\Groups({"booking"})
     */
    protected $id;

    /**
     * @ORM\Column(type="smallint")
     * @Serializer\Groups({"booking"})
     */
    protected $status = self::STATUS_NEW;

    /**
     * @ORM\ManyToOne(targetEntity=Schedule::class, inversedBy="bookings")
     * @Serializer\Groups({"booking_schedule"})
     */
    protected $schedule;

    /**
     * @ORM\Column(type="datetime")
     * @Serializer\Groups({"booking", "booking_start"})
     */
    protected $start;

    /**
     * @ORM\Column(type="datetime")
     * @Serializer\Groups({"booking", "booking_end"})
     */
    protected $end;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Groups({"booking_title"})
     */
    protected $title;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     * @Serializer\Groups({"booking"})
     */
    protected $customerComment;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="bookings")
     * @ORM\JoinColumn(nullable=true)
     * @Serializer\Groups({"booking_user"})
     */
    protected $user;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Groups({"booking"})
     */
    protected $userName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Groups({"booking"})
     */
    protected $userPhone;

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

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?\DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(\DateTimeInterface $end): self
    {
        $this->end = $end;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getCustomerComment(): ?string
    {
        return $this->customerComment;
    }

    public function setCustomerComment(?string $customerComment): self
    {
        $this->customerComment = $customerComment;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function setUserName(?string $userName): self
    {
        $this->userName = $userName;

        return $this;
    }

    public function getUserPhone(): ?string
    {
        return $this->userPhone;
    }

    public function setUserPhone(?string $userPhone): self
    {
        $this->userPhone = $userPhone;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): self
    {
        $this->status = $status;

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
     * @param DateTime $createdAt
     */
    public function setCreatedAt(DateTime $createdAt): self
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
     * @param DateTime $updatedAt
     * @return Booking
     */
    public function setUpdatedAt(DateTime $updatedAt): self
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
