<?php

namespace App\Model\DTO\Telegram;

use App\Model\DTO\DTOInterface;
use JMS\Serializer\Annotation as Serializer;

class CallbackQueryDTO implements DTOInterface
{
    /**
     * @Serializer\Type("int")
     */
    protected ?int $id = null;

    /**
     * @Serializer\Type("App\Model\DTO\Telegram\MessageFromDTO")
     */
    protected ?MessageFromDTO $from = null;

    /**
     * @Serializer\Type("App\Model\DTO\Telegram\MessageDTO")
     */
    protected ?MessageDTO $message = null;

    /**
     * @Serializer\Type("int")
     */
    protected ?int $chatInstance = null;

    /**
     * @Serializer\Type("string")
     */
    protected ?string $data = null;

    /**
     * CallbackQueryDTO constructor.
     * @param int|null $id
     * @param MessageFromDTO|null $from
     * @param MessageDTO|null $message
     * @param int|null $chatInstance
     * @param string|null $data
     */
    public function __construct(
        ?int $id,
        ?MessageFromDTO $from,
        ?MessageDTO $message,
        ?int $chatInstance,
        ?string $data
    ) {
        $this->id = $id;
        $this->from = $from;
        $this->message = $message;
        $this->chatInstance = $chatInstance;
        $this->data = $data;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return MessageFromDTO|null
     */
    public function getFrom(): ?MessageFromDTO
    {
        return $this->from;
    }

    /**
     * @return MessageDTO|null
     */
    public function getMessage(): ?MessageDTO
    {
        return $this->message;
    }

    /**
     * @return int|null
     */
    public function getChatInstance(): ?int
    {
        return $this->chatInstance;
    }

    /**
     * @return string|null
     */
    public function getData(): ?string
    {
        return $this->data;
    }
}
