<?php

namespace App\Model\DTO\Telegram;

use App\Model\DTO\BotMessageDTOInterface;
use JMS\Serializer\Annotation as Serializer;

class UpdateDTO implements BotMessageDTOInterface
{
    /**
     * @Serializer\Type("int")
     */
    protected ?int $updateId = null;

    /**
     * @Serializer\Type("App\Model\DTO\Telegram\MessageDTO")
     */
    protected ?MessageDTO $message = null;

    /**
     * @Serializer\Type("App\Model\DTO\Telegram\CallbackQueryDTO")
     */
    protected ?CallbackQueryDTO $callbackQuery = null;

    /**
     * UpdateDTO constructor.
     * @param int|null $updateId
     * @param MessageDTO|null $message
     * @param CallbackQueryDTO|null $callbackQuery
     */
    public function __construct(?int $updateId, ?MessageDTO $message, ?CallbackQueryDTO $callbackQuery)
    {
        $this->updateId = $updateId;
        $this->message = $message;
        $this->callbackQuery = $callbackQuery;
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

    /**
     * @return CallbackQueryDTO|null
     */
    public function getCallbackQuery(): ?CallbackQueryDTO
    {
        return $this->callbackQuery;
    }
}
