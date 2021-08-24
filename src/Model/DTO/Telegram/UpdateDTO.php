<?php

namespace App\Model\DTO\Telegram;

use App\Model\DTO\DTOInterface;
use JMS\Serializer\Annotation as Serializer;

class UpdateDTO implements DTOInterface
{
    /**
     * @Serializer\Type("int")
     */
    protected ?int $updateId;

    /**
     * @Serializer\Type("App\Model\DTO\Telegram\MessageDTO")
     */
    protected ?MessageDTO $message;

    /**
     * UpdateDTO constructor.
     * @param int|null $updateId
     * @param MessageDTO|null $message
     */
    public function __construct(?int $updateId, ?MessageDTO $message)
    {
        $this->updateId = $updateId;
        $this->message = $message;
    }

    /**
     * @return int|null
     */
    public function getUpdateId(): ?int
    {
        return $this->updateId;
    }

    /**
     * @return MessageDTO|null
     */
    public function getMessage(): ?MessageDTO
    {
        return $this->message;
    }
}
