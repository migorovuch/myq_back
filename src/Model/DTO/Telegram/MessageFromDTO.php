<?php

namespace App\Model\DTO\Telegram;

use App\Model\DTO\DTOInterface;
use JMS\Serializer\Annotation as Serializer;

class MessageFromDTO implements DTOInterface
{
    /**
     * @Serializer\Type("int")
     */
    protected ?int $id = null;

    /**
     * @Serializer\Type("boolean")
     */
    protected ?bool $isBoot = null;

    /**
     * @Serializer\Type("string")
     */
    protected ?string $firstName = null;

    /**
     * @Serializer\Type("string")
     */
    protected ?string $lastName = null;

    /**
     * @Serializer\Type("string")
     */
    protected ?string $username = null;

    /**
     * @Serializer\Type("string")
     */
    protected ?string $languageCode = null;

    /**
     * MessageFromDTO constructor.
     *
     * @param int|null    $id
     * @param bool|null   $isBoot
     * @param string|null $firstName
     * @param string|null $lastName
     * @param string|null $username
     * @param string|null $languageCode
     */
    public function __construct(
        ?int $id,
        ?bool $isBoot,
        ?string $firstName,
        ?string $lastName,
        ?string $username,
        ?string $languageCode
    ) {
        $this->id = $id;
        $this->isBoot = $isBoot;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->username = $username;
        $this->languageCode = $languageCode;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return bool|null
     */
    public function getIsBoot(): ?bool
    {
        return $this->isBoot;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @return string|null
     */
    public function getLanguageCode(): ?string
    {
        return $this->languageCode;
    }
}
