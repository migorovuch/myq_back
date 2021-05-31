<?php

namespace App\Util\DTOExporter;

use App\Entity\Company;
use App\Model\DTO\DTOInterface;
use App\Model\Model\EntityInterface;
use App\Util\Factory\PropertyInfoExtractorFactory;
use Symfony\Component\Security\Core\Security;

class CompanyDTOExporter extends DTOExporter
{
    /**
     * @var Security
     */
    protected Security $security;

    /**
     * CompanyDTOExporte constructor.
     * @param PropertyInfoExtractorFactory $propertyInfoExtractorFactory
     * @param Security $security
     */
    public function __construct(PropertyInfoExtractorFactory $propertyInfoExtractorFactory, Security $security)
    {
        parent::__construct($propertyInfoExtractorFactory);
        $this->security = $security;
    }

    /**
     * @param EntityInterface $entity
     * @param DTOInterface $dto
     * @param bool $setNullProperty
     * @return EntityInterface
     */
    public function exportDTO(EntityInterface $entity, DTOInterface $dto, bool $setNullProperty = true): EntityInterface
    {
        /** @var Company $entity */
        $entity = parent::exportDTO($entity, $dto, $setNullProperty);
        return $entity->setUser($this->security->getUser());
    }
}
