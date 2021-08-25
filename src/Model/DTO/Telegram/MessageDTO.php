<?php

namespace App\Model\DTO\Telegram;

use App\Model\DTO\DTOInterface;
use DateTimeInterface;
use JMS\Serializer\Annotation as Serializer;

class MessageDTO implements DTOInterface
{
    /**
     * @Serializer\Type("int")
     */
    protected ?int $messageId;

    /**
     * @Serializer\Type("App\Model\DTO\Telegram\MessageFromDTO")
     */
    protected ?MessageFromDTO $from;

    /**
     * @Serializer\Type("App\Model\DTO\Telegram\MessageChatDTO")
     */
    protected ?MessageChatDTO $chat;

    /**
     * @Serializer\Type("DateTime<'U'>")
     */
    protected ?DateTimeInterface $date = null;

    /**
     * @Serializer\Type("string")
     */
    protected ?string $text;

    /**
     * @Serializer\Type("array")
     */
    protected ?array $entities;

    /**
     * MessageDTO constructor.
     * @param int|null $messageId
     * @param MessageFromDTO|null $from
     * @param MessageChatDTO|null $chat
     * @param DateTimeInterface|null $date
     * @param string|null $text
     * @param array|null $entities
     */
    public function __construct(
        ?int $messageId,
        ?MessageFromDTO $from,
        ?MessageChatDTO $chat,
        ?DateTimeInterface $date,
        ?string $text,
        ?array $entities
    ) {
        $this->messageId = $messageId;
        $this->from = $from;
        $this->chat = $chat;
        $this->date = $date;
        $this->text = $text;
        $this->entities = $entities;
    }

    /**
     * @return int|null
     */
    public function getMessageId(): ?int
    {
        return $this->messageId;
    }

    /**
     * @return MessageFromDTO|null
     */
    public function getFrom(): ?MessageFromDTO
    {
        return $this->from;
    }

    /**
     * @return MessageChatDTO|null
     */
    public function getChat(): ?MessageChatDTO
    {
        return $this->chat;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    /**
     * @return string|null
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @return array|null
     */
    public function getEntities(): ?array
    {
        return $this->entities;
    }
}
