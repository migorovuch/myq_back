<?php

namespace App\Model\DTO\Company;

use App\Model\DTO\DTOInterface;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class CompanyDTO implements DTOInterface
{

    /**
     * @Assert\Type("string")
     * @Serializer\Type("string")
     */
    protected ?string $name = null;

    /**
     * @Assert\Type("string")
     * @Serializer\Type("string")
     */
    protected ?string $email = null;

    /**
     * @Assert\Type("string")
     * @Serializer\Type("string")
     */
    protected ?string $phone = null;

    /**
     * @Assert\Type("string")
     * @Serializer\Type("string")
     */
    protected ?string $address = null;

    /**
     * @Assert\Type("string")
     * @Serializer\Type("string")
     */
    protected ?string $addressLink = null;

    /**
     * @Assert\Type("string")
     * @Serializer\Type("string")
     */
    protected ?string $description = null;

    /**
     * CompanyDTO constructor.
     * @param string|null $name
     * @param string|null $email
     * @param string|null $phone
     * @param string|null $address
     * @param string|null $addressLink
     * @param string|null $description
     */
    public function __construct(
        ?string $name,
        ?string $email,
        ?string $phone,
        ?string $address,
        ?string $addressLink,
        ?string $description
    ) {
        $this->email = $email;
        $this->phone = $phone;
        $this->address = $address;
        $this->addressLink = $addressLink;
        $this->description = $description;
        $this->name = $name;
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
    public function getAddressLink(): ?string
    {
        return $this->addressLink;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

}
