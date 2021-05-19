<?php

namespace App\Model\DTO;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class AbstractFindDTO.
 */
abstract class AbstractFindDTO implements DTOInterface
{

    const CONDITION_AND = 'and';
    const CONDITION_OR = 'or';

    /**
     * @var string
     * @Serializer\Type("string")
     */
    protected $condition = self::CONDITION_AND;

    /**
     * @var string
     * @Serializer\Type("string")
     */
    protected $sort;

    /**
     * @var PageDTO
     * @Serializer\Type("App\Model\DTO\PageDTO")
     */
    protected $page;

    /**
     * AbstractFindDTO constructor.
     *
     * @param string  $sort
     * @param PageDTO $page
     * @param string  $condition
     */
    public function __construct(string $sort = null, PageDTO $page = null, string $condition = self::CONDITION_AND)
    {
        $this->sort = $sort;
        $this->page = $page;
        $this->condition = $condition;
    }
    /**
     * @Serializer\PostDeserialize()
     */
    public function postDeserialize()
    {
        $this->page = $this->page ?? new PageDTO();
    }

    /**
     * @return string|null
     */
    public function getSort(): ?string
    {
        return $this->sort;
    }

    /**
     * @return PageDTO|null
     */
    public function getPage(): ?PageDTO
    {
        return $this->page;
    }

    /**
     * @return string
     */
    public function getCondition(): string
    {
        return $this->condition;
    }

}
