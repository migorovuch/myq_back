<?php

namespace App\Model\DTO\CompanyChat;

use App\Entity\Company;
use App\Model\DTO\AbstractFindDTO;
use App\Model\DTO\PageDTO;
use JMS\Serializer\Annotation as Serializer;

class CompanyChatFindDTO extends AbstractFindDTO
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
    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private ?string $payload;

    /**
     * CompanyChatDTO constructor.
     *
     * @param Company|null $company
     * @param string|null  $chatId
     * @param string|null  $chatLanguage
     */
    public function __construct(
        ?Company $company = null,
        ?string $chatId = null,
        ?string $chatLanguage = null,
        ?string $payload = null,
        ?array $sort = null,
        ?PageDTO $page = null,
        ?string $condition = self::CONDITION_AND
    ) {
        parent::__construct($sort, $page, $condition);
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
