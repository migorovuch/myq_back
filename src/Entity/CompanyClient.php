<?php

namespace App\Entity;

use App\Model\Model\EntityInterface;
use App\Repository\CompanyClientRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=CompanyClientRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class CompanyClient implements EntityInterface
{

    const STATUS_OFF = 0;
    const STATUS_ON = 1;
    const STATUS_DEACTIVATED = 2;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     * @Serializer\Groups({"company_client", "company_client_id"})
     */
    protected string $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups({"company_client"})
     */
    protected string $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups({"company_client"})
     */
    protected string $phone;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=true)
     * @Serializer\Groups({"company_client_user"})
     */
    protected ?User $user = null;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class)
     * @Serializer\Groups({"company_client_company"})
     */
    protected ?Company $company = null;

    /**
     * @ORM\Column(type="smallint")
     * @Serializer\Groups({"company_client_status"})
     */
    protected int $status = self::STATUS_ON;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Groups({"company_client_pseudonym"})
     */
    protected ?string $pseudonym;

    /**
     * @ORM\Column(type="datetime")
     * @Serializer\Groups({"company_client"})
     */
    protected ?DateTimeInterface $createdAt = null;

    /**
     * @ORM\Column(type="datetime")
     * @Serializer\Groups({"company_client"})
     */
    protected ?DateTimeInterface $updatedAt = null;

    /**
     * @ORM\OneToMany(targetEntity=Booking::class, mappedBy="client")
     * @Serializer\Groups({"company_client_bookings"})
     */
    private $bookings;

    /**
     * @var int
     * @Serializer\Groups({"company_client_number_of_bookings"})
     */
    protected int $numberOfBookings = 0;

    /**
     * CompanyClient constructor.
     */
    public function __construct()
    {
        $this->bookings = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        if ($user) {
            $this->user = $user;
            if ($user->getPhone()) {
                $this->setPhone($user->getPhone());
            }
            if ($user->getFullName()) {
                $this->setName($user->getFullName());
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getPseudonym(): ?string
    {
        return $this->pseudonym;
    }

    public function setPseudonym(?string $pseudonym): self
    {
        $this->pseudonym = $pseudonym;

        return $this;
    }

    /**
     * @return Company|null
     */
    public function getCompany(): ?Company
    {
        return $this->company;
    }

    /**
     * @param Company|null $company
     * @return CompanyClient
     */
    public function setCompany(?Company $company): self
    {
        $this->company = $company;

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

    /**
     * @return int
     */
    public function getNumberOfBookings(): int
    {
        return $this->numberOfBookings;
    }

    /**
     * @param int $numberOfBookings
     * @return CompanyClient
     */
    public function setNumberOfBookings(int $numberOfBookings): self
    {
        $this->numberOfBookings = $numberOfBookings;

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
            $booking->setClient($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): self
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getClient() === $this) {
                $booking->setClient(null);
            }
        }

        return $this;
    }
}
