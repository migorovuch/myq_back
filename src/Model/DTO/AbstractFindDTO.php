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
     * @var array|null
     * @Serializer\Type("array")
     */
    protected ?array $sort = null;

    /**
     * @var PageDTO
     * @Serializer\Type("App\Model\DTO\PageDTO")
     */
    protected $page = null;

    /**
     * AbstractFindDTO constructor.
     *
     * @param array|null $sort
     * @param PageDTO|null $page
     * @param string $condition
     */
    public function __construct(array $sort = null, PageDTO $page = null, string $condition = self::CONDITION_AND)
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
     * @return array|null
     */
    public function getSort(): ?array
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
