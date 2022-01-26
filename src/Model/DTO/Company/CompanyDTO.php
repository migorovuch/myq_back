<?php

namespace App\Model\DTO\Company;

use App\Model\DTO\DTOInterface;
use App\Validator\ConstraintCompanyUniqueSlug;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ConstraintCompanyUniqueSlug
 */
class CompanyDTO implements DTOInterface
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
     * @Assert\Type("integer", groups={"Default"})
     * @Serializer\Type("integer")
     */
    protected ?int $timezoneoffset = null;

    /**
     * @Assert\Type("string", groups={"Default"})
     * @Serializer\Type("string")
     */
    protected ?string $address = null;

    /**
     * @Assert\Type("string", groups={"Default"})
     * @Serializer\Type("string")
     */
    protected ?string $addressLink = null;

    /**
     * @Assert\Type("string", groups={"Default"})
     * @Serializer\Type("string")
     */
    protected ?string $description = null;

    /**
     * @Assert\Type("string", groups={"Default"})
     * @Serializer\Type("string")
     */
    protected ?string $logo = null;

    /**
     * @Assert\Type("string", groups={"Default"})
     * @Assert\Regex("/^[a-z0-9]+(-?[a-z0-9]+)*$/i")
     * @Serializer\Type("string")
     */
    protected ?string $slug;

    /**
     * CompanyDTO constructor.
     *
     * @param string|null $name
     * @param string|null $email
     * @param string|null $phone
     * @param string|null $address
     * @param string|null $addressLink
     * @param string|null $description
     * @param string|null $logo
     * @param int|null $timezoneoffset
     * @param string|null $slug
     */
    public function __construct(
        ?string $name = null,
        ?string $email = null,
        ?string $phone = null,
        ?string $address = null,
        ?string $addressLink = null,
        ?string $description = null,
        ?string $logo = null,
        ?int $timezoneoffset = null,
        ?string $slug = null
    ) {
        $this->email = $email;
        $this->phone = $phone;
        $this->address = $address;
        $this->addressLink = $addressLink;
        $this->description = $description;
        $this->name = $name;
        $this->logo = $logo;
        $this->timezoneoffset = $timezoneoffset;
        $this->slug = $slug;
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

    /**
     * @return string|null
     */
    public function getLogo(): ?string
    {
        return $this->logo;
    }

    /**
     * @return int|null
     */
    public function getTimezoneoffset(): ?int
    {
        return $this->timezoneoffset;
    }

    /**
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }
}
