<?php

namespace App\Model\DTO\Booking;

use App\Entity\Company;
use App\Entity\CompanyClient;
use App\Entity\Schedule;
use App\Entity\User;
use App\Model\DTO\AbstractFindDTO;
use App\Model\DTO\PageDTO;
use DateTime;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class BookingFindDTO extends AbstractFindDTO
{

    /**
     * @Assert\Type("string", groups={"Default"})
     * @Serializer\Type("string")
     * @var string|null
     */
    protected ?string $id = null;

    /**
     * @Assert\Type("int", groups={"Default"})
     * @Assert\Choice(choices=App\Entity\Booking::STATUS_LIST, message="Wrong status selected", groups={"Default"})
     * @Serializer\Type("integer")
     * @var int|null
     */
    protected ?int $status = null;

    /**
     * @Assert\Type("string", groups={"Default"})
     * @Serializer\Type("string")
     */
    protected ?string $scheduleName = null;

    /**
     * @Assert\Type("App\Entity\Schedule", groups={"Default"})
     * @Assert\NotBlank(groups={"booking_schedule"}, message="This value should not be blank")
     * @Serializer\Type("Relation<App\Entity\Schedule>")
     */
    protected ?Schedule $schedule = null;

    /**
     * @Assert\Type("string", groups={"Default"})
     * @Serializer\Type("string")
     */
    protected ?string $companyName = null;

    /**
     * @Assert\Type("App\Entity\Company", groups={"Default"})
     * @Assert\NotBlank(groups={"booking_company"}, message="This value should not be blank")
     * @Serializer\Type("Relation<App\Entity\Company>")
     */
    protected ?Company $company = null;

    /**
     * @Assert\Type("App\Entity\CompanyClient", groups={"Default"})
     * @Serializer\Type("Relation<App\Entity\CompanyClient>")
     */
    protected ?CompanyClient $client = null;

    /**
     * @Assert\Type("\DateTimeInterface", groups={"Default"})
     * @Serializer\Type("DateTime<'U'>")
     */
    protected ?DateTime $filterFrom = null;

    /**
     * @Assert\Type("\DateTimeInterface", groups={"Default"})
     * @Serializer\Type("DateTime<'U'>")
     */
    protected ?DateTime $filterTo = null;

    /**
     * @Assert\Type("string", groups={"Default"})
     * @Serializer\Type("string")
     */
    protected ?string $title = null;

    /**
     * @Assert\Type("string", groups={"Default"})
     * @Serializer\Type("string")
     */
    protected ?string $customerComment = null;

    /**
     * @Assert\Type("App\Entity\User", groups={"Default"})
     * @Assert\NotBlank(groups={"booking_user"}, message="This value should not be blank")
     * @Serializer\Type("Relation<App\Entity\User>")
     */
    protected ?User $user = null;

    /**
     * @Assert\Type("string", groups={"Default"})
     * @Serializer\Type("string")
     */
    protected ?string $userName = null;

    /**
     * @Assert\Type("string", groups={"Default"})
     * @Serializer\Type("string")
     */
    protected ?string $userPhone = null;

    /**
     * BookingDTO constructor.
     * @param string|null $id
     * @param int|null $status
     * @param string|null $scheduleName
     * @param Schedule|null $schedule
     * @param string|null $companyName
     * @param Company|null $company
     * @param DateTime|null $filterFrom
     * @param DateTime|null $filterTo
     * @param string|null $title
     * @param string|null $customerComment
     * @param CompanyClient|null $client
     * @param User|null $user
     * @param string|null $userName
     * @param string|null $userPhone
     * @param array|null $sort
     * @param PageDTO|null $page
     * @param string|null $condition
     */
    public function __construct(
        ?string $id = null,
        ?int $status = null,
        ?string $scheduleName = null,
        ?Schedule $schedule = null,
        ?string $companyName = null,
        ?Company $company = null,
        ?DateTime $filterFrom = null,
        ?DateTime $filterTo = null,
        ?string $title = null,
        ?string $customerComment = null,
        ?CompanyClient $client = null,
        ?User $user = null,
        ?string $userName = null,
        ?string $userPhone = null,
        ?array $sort = null,
        ?PageDTO $page = null,
        ?string $condition = self::CONDITION_AND
    ) {
        parent::__construct($sort, $page, $condition);
        $this->id = $id;
        $this->status = $status;
        $this->schedule = $schedule;
        $this->filterFrom = $filterFrom;
        $this->filterTo = $filterTo;
        $this->title = $title;
        $this->customerComment = $customerComment;
        $this->user = $user;
        $this->userName = $userName;
        $this->userPhone = $userPhone;
        $this->company = $company;
        $this->client = $client;
        $this->scheduleName = $scheduleName;
        $this->companyName = $companyName;
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return Schedule|null
     */
    public function getSchedule(): ?Schedule
    {
        return $this->schedule;
    }

    /**
     * @return Company|null
     */
    public function getCompany(): ?Company
    {
        return $this->company;
    }

    /**
     * @return string|null
     */
    public function getScheduleName(): ?string
    {
        return $this->scheduleName;
    }

    /**
     * @return string|null
     */
    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    /**
     * @return DateTime|null
     */
    public function getFilterFrom(): ?DateTime
    {
        return $this->filterFrom;
    }

    /**
     * @return DateTime|null
     */
    public function getFilterTo(): ?DateTime
    {
        return $this->filterTo;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @return string|null
     */
    public function getCustomerComment(): ?string
    {
        return $this->customerComment;
    }

    /**
     * @return CompanyClient|null
     */
    public function getClient(): ?CompanyClient
    {
        return $this->client;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @return string|null
     */
    public function getUserName(): ?string
    {
        return $this->userName;
    }

    /**
     * @return string|null
     */
    public function getUserPhone(): ?string
    {
        return $this->userPhone;
    }

    /**
     * @return int|null
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @param User|null $user
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @param string|null $userName
     */
    public function setUserName(?string $userName): self
    {
        $this->userName = $userName;

        return $this;
    }

    /**
     * @param string|null $userPhone
     */
    public function setUserPhone(?string $userPhone): self
    {
        $this->userPhone = $userPhone;

        return $this;
    }
}
