<?php

namespace App\Entity;

use App\Model\Model\EntityInterface;
use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass=CompanyRepository::class)
 */
class Company implements EntityInterface
{
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     * @Serializer\Groups({"company", "company_id"})
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="companies")
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\Groups({"company_user"})
     */
    protected $user;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Groups({"company"})
     */
    protected $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Groups({"company"})
     */
    protected $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Groups({"company"})
     */
    protected $logo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Groups({"company"})
     */
    protected $address;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Groups({"company"})
     */
    protected $addressLink;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Serializer\Groups({"company"})
     */
    protected $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Groups({"company"})
     */
    protected $photos;

    /**
     * @ORM\Column(type="smallint")
     * @Serializer\Groups({"company"})
     */
    private $status = self::STATUS_ON;

    /**
     * @ORM\OneToMany(targetEntity=Schedule::class, mappedBy="company", orphanRemoval=true)
     * @Serializer\Groups({"company_schedules"})
     */
    private $schedules;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Groups({"company", "company_name"})
     */
    private $name;

    public function __construct()
    {
        $this->schedules = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getAddressLink(): ?string
    {
        return $this->addressLink;
    }

    public function setAddressLink(?string $addressLink): self
    {
        $this->addressLink = $addressLink;

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

    public function getPhotos(): ?string
    {
        return $this->photos;
    }

    public function setPhotos(?string $photos): self
    {
        $this->photos = $photos;

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

    /**
     * @return Collection|Schedule[]
     */
    public function getSchedules(): Collection
    {
        return $this->schedules;
    }

    public function addSchedule(Schedule $schedule): self
    {
        if (!$this->schedules->contains($schedule)) {
            $this->schedules[] = $schedule;
            $schedule->setCompany($this);
        }

        return $this;
    }

    public function removeSchedule(Schedule $schedule): self
    {
        if ($this->schedules->removeElement($schedule)) {
            // set the owning side to null (unless already changed)
            if ($schedule->getCompany() === $this) {
                $schedule->setCompany(null);
            }
        }

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
