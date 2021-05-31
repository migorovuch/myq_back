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
     * @param Company|null $company
     * @param string|null $name
     * @param string|null $enabled
     */
    public function __construct(?Company $company, ?string $name, ?string $enabled, ?string $sort, ?PageDTO $page, string $condition = self::CONDITION_AND)
    {
        parent::__construct($sort, $page, $condition);
        $this->company = $company;
        $this->name = $name;
        $this->enabled = $enabled;
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
