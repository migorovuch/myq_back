<?php

namespace App\Model\DTO\CompanyChat;

use App\Entity\Company;
use App\Entity\CompanyChat;
use App\Model\DTO\DTOInterface;
use JMS\Serializer\Annotation as Serializer;

class CompanyChatDTO implements DTOInterface
{
    /**
     * @Serializer\Type("Relation<App\Entity\Company>")
     */
    protected ?Company $company;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    protected ?string $chatId;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    protected ?string $chatLanguage;
    private ?string $payload;

    /**
     * CompanyChatDTO constructor.
     * @param Company|null $company
     * @param string|null $chatId
     * @param string|null $chatLanguage
     */
    public function __construct(
        ?Company $company = null,
        ?string $chatId = null,
        ?string $chatLanguage = CompanyChat::DEFAULT_CHAT_LANGUAGE,
        ?string $payload = null
    ) {
        $this->company = $company;
        $this->chatId = $chatId;
        $this->chatLanguage = $chatLanguage;
        $this->payload = $payload;
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
    public function getChatId(): ?string
    {
        return $this->chatId;
    }

    /**
     * @return string|null
     */
    public function getChatLanguage(): ?string
    {
        return $this->chatLanguage;
    }

    /**
     * @return string|null
     */
    public function getPayload(): ?string
    {
        return $this->payload;
    }
    
}
