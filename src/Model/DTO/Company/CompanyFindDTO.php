<?php

namespace App\Model\DTO\Company;

use App\Entity\User;
use App\Model\DTO\AbstractFindDTO;
use App\Model\DTO\PageDTO;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class CompanyFindDTO extends AbstractFindDTO
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
    protected ?string $email = null;

    /**
     * @Assert\Type("string", groups={"Default"})
     * @Serializer\Type("string")
     */
    protected ?string $phone = null;

    /**
     * @Assert\Type("string", groups={"Default"})
     * @Serializer\Type("string")
     */
    protected ?string $address = null;

    /**
     * @Assert\Type("integer")
     * @Serializer\Type("integer")
     */
    protected ?int $status = null;

    /**
     * @Assert\Type("string", groups={"Default"})
     * @Serializer\Type("Relation<App\Entity\User>")
     */
    protected ?User $user = null;

    /**
     * CompanyDTO constructor.
     *
     * @param string|null  $id
     * @param string|null  $name
     * @param string|null  $email
     * @param string|null  $phone
     * @param string|null  $address
     * @param int|null     $status
     * @param string|null  $user
     * @param array|null   $sort
     * @param PageDTO|null $page
     * @param string|null  $condition
     */
    public function __construct(
        string $id = null,
        string $name = null,
        string $email = null,
        string $phone = null,
        string $address = null,
        int $status = null,
        string $user = null,
        array $sort = null,
        PageDTO $page = null,
        ?string $condition = self::CONDITION_AND
    ) {
        parent::__construct($sort, $page, $condition);
        $this->email = $email;
        $this->phone = $phone;
        $this->address = $address;
        $this->name = $name;
        $this->user = $user;
        $this->status = $status;
        $this->id = $id;
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
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @return User|string|null
     */
    public function getUser(): User | string | null
    {
        return $this->user;
    }
}
