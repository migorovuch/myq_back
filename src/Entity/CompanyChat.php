<?php

namespace App\Entity;

use App\Model\Model\EntityInterface;
use App\Repository\CompanyChatRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass=CompanyChatRepository::class)
 */
class CompanyChat implements EntityInterface
{
    const DEFAULT_CHAT_LANGUAGE = 'uk';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     * @Serializer\Groups({"company_chat", "company_chat_id"})
     */
    protected string $id;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class)
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     * @Serializer\Groups({"company_chat", "company_chat_company"})
     */
    protected ?Company $company = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups({"company_chat", "company_chat_chat_id"})
     */
    protected string $chatId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected string $chatLanguage = self::DEFAULT_CHAT_LANGUAGE;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $payload;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getChatId(): ?string
    {
        return $this->chatId;
    }

    public function setChatId(string $chatId): self
    {
        $this->chatId = $chatId;

        return $this;
    }

    public function getChatLanguage(): ?string
    {
        return $this->chatLanguage;
    }

    public function setChatLanguage(string $chatLanguage): self
    {
        $this->chatLanguage = $chatLanguage;

        return $this;
    }

    public function getPayload(): ?string
    {
        return $this->payload;
    }

    public function setPayload(?string $payload): self
    {
        $this->payload = $payload;

        return $this;
    }
}
