<?php

namespace App\Entity;

use App\Model\Model\EntityInterface;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface, EntityInterface
{
    const ROLE_USER = 'ROLE_USER';
    const ROLE_ADMIN = 'ROLE_ADMIN';

    const STATUS_OFF = 0;
    const STATUS_ON = 1;
    const STATUS_DEACTIVATED = 2;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     * @Serializer\Groups({"user"})
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups({"user", "user_email"})
     */
    protected $email;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Serializer\Groups({"user", "user_nickname"})
     */
    protected $nickname;

    /**
     * @ORM\Column(type="json")
     * @Serializer\Groups({"user"})
     */
    protected $roles = [self::ROLE_USER];

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups({"user_password"})
     */
    protected $password;

    /**
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"user"})
     */
    protected $status = self::STATUS_ON;

    /**
     * @ORM\Column(type="string")
     * @Serializer\Groups({"user", "user_phone"})
     */
    protected ?string $phone;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @Serializer\Groups({"user"})
     */
    protected $dateCreate;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update")
     * @Serializer\Groups({"user"})
     */
    protected $dateUpdate;

    /**
     * @ORM\OneToMany(targetEntity=Company::class, mappedBy="user", orphanRemoval=true)
     */
    protected $companies;

    /**
     * @ORM\OneToMany(targetEntity=Booking::class, mappedBy="user", orphanRemoval=true)
     */
    protected $bookings;

    public function __construct()
    {
        $this->companies = new ArrayCollection();
        $this->bookings = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    /**
     * @param string $nickname
     *
     * @return User
     */
    public function setNickname(string $nickname): self
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function getRoles(): ?array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = self::ROLE_USER;

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

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

    public function getDateCreate(): ?\DateTimeInterface
    {
        return $this->dateCreate;
    }

    public function setDateCreate(\DateTimeInterface $dateCreate): self
    {
        $this->dateCreate = $dateCreate;

        return $this;
    }

    public function getDateUpdate(): ?\DateTimeInterface
    {
        return $this->dateUpdate;
    }

    public function setDateUpdate(\DateTimeInterface $dateUpdate): self
    {
        $this->dateUpdate = $dateUpdate;

        return $this;
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function getUsername()
    {
        return $this->nickname;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @return array
     */
    public static function getRolesList()
    {
        return [
            self::ROLE_USER => self::ROLE_USER,
            self::ROLE_ADMIN => self::ROLE_ADMIN,
        ];
    }

    /**
     * @return array
     */
    public static function getPublicRolesList()
    {
        return [
            self::ROLE_USER => self::ROLE_USER,
        ];
    }

    /**
     * @param string $role
     *
     * @return bool
     */
    public function isRole(string $role)
    {
        return in_array($role, $this->getRoles());
    }

    /**
     * @return Collection|Company[]
     */
    public function getCompanies(): Collection
    {
        return $this->companies;
    }

    public function addCompany(Company $company): self
    {
        if (!$this->companies->contains($company)) {
            $this->companies[] = $company;
            $company->setUser($this);
        }

        return $this;
    }

    public function getFirstCompany(): ?Company
    {
        $companies = $this->getCompanies();
        if ($companies instanceof Collection) {
            if ($companies->isEmpty()) {
                $companies = null;
            } else {
                $companies = $companies->first();
            }
        } elseif (is_array($companies) && !empty($companies)) {
            $companies = $companies[array_key_first($companies)];
        } elseif (!$companies) {
            $companies = null;
        }

        return $companies;
    }

    public function removeCompany(Company $company): self
    {
        if ($this->companies->removeElement($company)) {
            // set the owning side to null (unless already changed)
            if ($company->getUser() === $this) {
                $company->setUser(null);
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
            $booking->setUser($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): self
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getUser() === $this) {
                $booking->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @param string|null $phone
     * @return self
     */
    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }
}
