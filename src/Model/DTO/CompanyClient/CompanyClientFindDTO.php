<?php

namespace App\Model\DTO\CompanyClient;

use App\Entity\Company;
use App\Entity\User;
use App\Model\DTO\AbstractFindDTO;
use App\Model\DTO\PageDTO;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class CompanyClientFindDTO extends AbstractFindDTO
{

    /**
     * @Assert\Type("string", groups={"Default"})
     * @Serializer\Type("string")
     */
    protected ?string $id = null;

    /**
     * @Assert\Type("string", groups={"Default"})
     * @Serializer\Type("string")
     */
    protected ?string $name = null;

    /**
     * @Assert\Type("string", groups={"Default"})
     * @Serializer\Type("string")
     */
    protected ?string $phone = null;

    /**
     * @Assert\Type("App\Entity\User", groups={"Default"})
     * @Serializer\Type("Relation<App\Entity\User>")
     */
    protected ?User $user = null;

    /**
     * @Assert\Type("App\Entity\Company", groups={"Default"})
     * @Assert\NotBlank(groups={"Default"})
     * @Serializer\Type("Relation<App\Entity\Company>")
     */
    protected ?Company $company = null;

    /**
     * @Assert\Type("int", groups={"Default"})
     * @Serializer\Type("integer")
     */
    protected ?int $status = null;

    /**
     * @Assert\Type("string")
     * @Serializer\Type("string")
     */
    protected ?string $pseudonym = null;

    /**
     * @Assert\Type("int", groups={"Default"})
     * @Serializer\Type("integer")
     */
    protected ?int $numberOfBookings = null;

    /**
     * CompanyClientFindDTO constructor.
     * @param string|null $id
     * @param string|null $name
     * @param string|null $phone
     * @param User|null $user
     * @param Company|null $company
     * @param int|null $status
     * @param string|null $pseudonym
     * @param int|null $numberOfBookings
     */
    public function __construct(
        ?string $id = null,
        ?string $name = null,
        ?string $phone = null,
        ?User $user = null,
        ?Company $company = null,
        ?int $status = null,
        ?string $pseudonym = null,
        ?int $numberOfBookings = null,
        ?array $sort = null,
        ?PageDTO $pageDTO = null,
        ?string $condition = self::CONDITION_AND
    ) {
        parent::__construct($sort, $pageDTO, $condition);
        $this->id = $id;
        $this->name = $name;
        $this->phone = $phone;
        $this->user = $user;
        $this->company = $company;
        $this->status = $status;
        $this->pseudonym = $pseudonym;
        $this->numberOfBookings = $numberOfBookings;
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @return Company|null
     */
    public function getCompany(): ?Company
    {
        return $this->company;
    }

    /**
     * @return int|null
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @return string|null
     */
    public function getPseudonym(): ?string
    {
        return $this->pseudonym;
    }

    /**
     * @return int|null
     */
    public function getNumberOfBookings(): ?int
    {
        return $this->numberOfBookings;
    }
}
