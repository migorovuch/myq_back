<?php

namespace App\Model\DTO\CompanyClient;

use App\Entity\Company;
use App\Entity\CompanyClient;
use App\Entity\User;
use App\Model\DTO\DTOInterface;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class CompanyClientDTO implements DTOInterface
{
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
     * @Serializer\Type("Relation<App\Entity\Company>")
     */
    protected ?Company $company = null;

    /**
     * @Assert\Type("int", groups={"Default"})
     * @Serializer\Type("integer")
     */
    protected ?int $status = null;

    /**
     * @Assert\Type("string", groups={"Default"})
     * @Serializer\Type("string")
     */
    protected ?string $pseudonym = null;

    /**
     * CompanyClientDTO constructor.
     *
     * @param User|null    $user
     * @param string|null  $name
     * @param string|null  $phone
     * @param Company|null $company
     * @param int          $status
     * @param string|null  $pseudonym
     */
    public function __construct(
        User $user = null,
        string $name = null,
        string $phone = null,
        Company $company = null,
        int $status = CompanyClient::STATUS_ON,
        string $pseudonym = null
    ) {
        $this->name = $name;
        $this->phone = $phone;
        $this->user = $user;
        $this->company = $company;
        $this->status = $status;
        $this->pseudonym = $pseudonym;
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
}
