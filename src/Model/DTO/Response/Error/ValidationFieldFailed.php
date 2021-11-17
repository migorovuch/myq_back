<?php

namespace App\Model\DTO\Response\Error;

use App\Model\DTO\DTOInterface;

/**
 * Class ValidationFailed.
 */
class ValidationFieldFailed implements DTOInterface
{
    public function __construct(protected string $source, protected string $title)
    {
    }

    /**
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }
}
