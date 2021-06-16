<?php

namespace App\Model\DTO\Booking;

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
     * @Assert\Type("string")
     * @Serializer\Type("string")
     * @var string|null
     */
    protected ?string $id = null;

    /**
     * @Assert\Type("int")
     * @Assert\Choice(choices=App\Entity\Booking::STATUS_LIST, message="Wrong status selected")
     * @Serializer\Type("integer")
     * @var int|null
     */
    protected ?int $status;

    /**
     * @Assert\Type("App\Entity\Schedule", groups={"Default"})
     * @Assert\NotBlank(groups={"Default"})
     * @Serializer\Type("Relation<App\Entity\Schedule>")
     */
    protected ?Schedule $schedule = null;

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
     * @Assert\Type("string")
     * @Serializer\Type("string")
     */
    protected ?string $title = null;

    /**
     * @Assert\Type("string")
     * @Serializer\Type("string")
     */
    protected ?string $customerComment = null;

    /**
     * @Assert\Type("App\Entity\User", groups={"Default"})
     * @Assert\NotBlank(groups={"Default"})
     * @Serializer\Type("Relation<App\Entity\User>")
     */
    protected ?User $user = null;

    /**
     * @Assert\Type("string")
     * @Serializer\Type("string")
     */
    protected ?string $userName = null;

    /**
     * @Assert\Type("string")
     * @Serializer\Type("string")
     */
    protected ?string $userPhone = null;

    /**
     * BookingDTO constructor.
     * @param string|null $id
     * @param int|null $status
     * @param Schedule|null $schedule
     * @param DateTime|null $filterFrom
     * @param DateTime|null $filterTo
     * @param string|null $title
     * @param string|null $customerComment
     * @param User|null $user
     * @param string|null $userName
     * @param string|null $userPhone
     * @param string|null $sort
     * @param PageDTO|null $page
     * @param string|null $condition
     */
    public function __construct(
        ?string $id = null,
        ?int $status = null,
        ?Schedule $schedule = null,
        ?DateTime $filterFrom = null,
        ?DateTime $filterTo = null,
        ?string $title = null,
        ?string $customerComment = null,
        ?User $user = null,
        ?string $userName = null,
        ?string $userPhone = null,
        ?string $sort = null,
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
}
