<?php

namespace App\Entity;

use App\Model\Model\EntityInterface;
use App\Repository\BookingRepository;
use DateTime;
use DateTimeZone;
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
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
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
     * @ORM\ManyToOne(targetEntity=CompanyClient::class, inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Serializer\Groups({"booking_client"})
     */
    protected ?CompanyClient $client;

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

    public function getHumanReadableTime($format = null, $format2 = null): string
    {
        $timezone = new DateTimeZone(
            timezone_name_from_abbr('', $this->getSchedule()->getCompany()->getTimezoneoffset(), 1)
        );
        $start = $this->getStart()->setTimezone($timezone);
        $end = $this->getEnd()->setTimezone($timezone);

        return $start->format($format ?? 'd/m (H:i').' - '.$end->format($format2 ?? 'H:i)');
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
     *
     * @return Booking
     */
    public function setUpdatedAt(DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return CompanyClient|null
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param CompanyClient|null $client
     *
     * @return Booking
     */
    public function setClient(?CompanyClient $client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps(): void
    {
        $this->setUpdatedAt(new DateTime('now'));
        if (null === $this->getCreatedAt()) {
            $this->setCreatedAt(new DateTime('now'));
        }
    }
}
