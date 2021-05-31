<?php

namespace App\Model\DTO\Schedule;

use App\Entity\Company;
use App\Model\DTO\AbstractFindDTO;
use App\Model\DTO\PageDTO;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class ScheduleFindDTO extends AbstractFindDTO
{

    /**
     * @Assert\Type("string")
     * @Serializer\Type("string")
     */
    protected ?string $id = null;

    /**
     * @Assert\Type("string")
     * @Serializer\Type("Relation<App\Entity\Company>")
     */
    protected ?Company $company = null;

    /**
     * @Assert\Type("string")
     * @Serializer\Type("string")
     */
    protected ?string $name = null;

    /**
     * @Assert\Type("boolean")
     * @Serializer\Type("boolean")
     */
    protected ?string $enabled = null;

    /**
     * ScheduleFindDTO constructor.
     * @param string|null $id
     * @param Company|null $company
     * @param string|null $name
     * @param string|null $enabled
     * @param string|null $sort
     * @param PageDTO|null $page
     * @param string $condition
     */
    public function __construct(?string $id, ?Company $company, ?string $name, ?string $enabled, ?string $sort, ?PageDTO $page, string $condition = self::CONDITION_AND)
    {
        parent::__construct($sort, $page, $condition);
        $this->company = $company;
        $this->name = $name;
        $this->enabled = $enabled;
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
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
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getEnabled(): ?string
    {
        return $this->enabled;
    }
}
