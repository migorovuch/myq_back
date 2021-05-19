<?php

namespace App\Model\DTO;

use JMS\Serializer\Annotation as Serializer;

class PageDTO
{
    const DEFAULT_LIMIT = 20;

    /**
     * @var int
     * @Serializer\Type("integer")
     */
    protected $limit;

    /**
     * @var int
     * @Serializer\Type("integer")
     */
    protected $offset;

    /**
     * Page constructor.
     *
     * @param int $limit
     * @param int $offset
     */
    public function __construct(int $limit = null, int $offset = null)
    {
        $this->limit = $limit ?? static::DEFAULT_LIMIT;
        $this->offset = $offset ?? 0;
    }

    /**
     * @Serializer\PostDeserialize()
     */
    public function postDeserialize()
    {
        $this->limit = $this->limit ?? static::DEFAULT_LIMIT;
        $this->offset = $this->offset ?? 0;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }
}
