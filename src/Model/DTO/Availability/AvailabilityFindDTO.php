<?php

namespace App\Model\DTO\Availability;

use App\Entity\Company;
use App\Entity\Schedule;
use App\Model\DTO\AbstractFindDTO;
use App\Model\DTO\PageDTO;
use DateTimeInterface;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class AvailabilityFindDTO extends AbstractFindDTO
{
    /**
     * @Assert\Type("App\Entity\Schedule", groups={"Default"})
     * @Assert\NotBlank(groups={"Default"}, message="This value should not be blank")
     * @Serializer\Type("Relation<App\Entity\Schedule>")
     */
    protected ?Schedule $schedule = null;

    /**
     * @Assert\Type("App\Entity\Company", groups={"Default"})
     * @Serializer\Type("Relation<App\Entity\Company>")
     */
    protected ?Company $company = null;

    /**
     * @Assert\Type("\DateTimeInterface", groups={"Default"})
     * @Assert\NotBlank(groups={"Default"}, message="This value should not be blank")
     * @Serializer\Type("DateTime<'U'>")
     */
    protected ?DateTimeInterface $filterFrom = null;

    /**
     * @Assert\Type("\DateTimeInterface", groups={"Default"})
     * @Assert\NotBlank(groups={"Default"}, message="This value should not be blank")
     * @Serializer\Type("DateTime<'U'>")
     */
    protected ?DateTimeInterface $filterTo = null;

    /**
     * AvailabilityFindDTO constructor.
     *
     * @param Schedule|null          $schedule
     * @param Company|null           $company
     * @param DateTimeInterface|null $filterFrom
     * @param DateTimeInterface|null $filterTo
     * @param array|null             $sort
     * @param PageDTO|null           $page
     * @param string                 $condition
     */
    public function __construct(
        ?Schedule $schedule = null,
        ?Company $company = null,
        ?DateTimeInterface $filterFrom = null,
        ?DateTimeInterface $filterTo = null,
        array $sort = null,
        PageDTO $page = null,
        string $condition = self::CONDITION_AND
    ) {
        parent::__construct($sort, $page, $condition);
        $this->schedule = $schedule;
        $this->filterFrom = $filterFrom;
        $this->filterTo = $filterTo;
        $this->company = $company;
    }

    /**
     * @return Schedule|null
     */
    public function getSchedule(): ?Schedule
    {
        return $this->schedule;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getFilterFrom(): ?DateTimeInterface
    {
        return $this->filterFrom;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getFilterTo(): ?DateTimeInterface
    {
        return $this->filterTo;
    }

    /**
     * @return Company|null
     */
    public function getCompany(): ?Company
    {
        return $this->company;
    }
}
