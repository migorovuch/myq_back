<?php

namespace App\Util\DTOExporter;

use App\Model\DTO\DTOInterface;
use App\Model\Model\EntityInterface;
use App\Util\Factory\PropertyInfoExtractorFactory;

/**
 * Class DTOExtractor.
 */
class DTOExporter implements DTOExporterInterface
{
    /**
     * @var PropertyInfoExtractorFactory
     */
    private $propertyInfoExtractorFactory;

    /**
     * DTOExporter constructor.
     *
     * @param PropertyInfoExtractorFactory $propertyInfoExtractorFactory
     */
    public function __construct(PropertyInfoExtractorFactory $propertyInfoExtractorFactory)
    {
        $this->propertyInfoExtractorFactory = $propertyInfoExtractorFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function exportDTO(EntityInterface $entity, DTOInterface $dto, bool $setNullProperty = true): EntityInterface
    {
        $propertyInfoExtractor = $this->propertyInfoExtractorFactory->buildPropertyInfoExtractor();
        $entityMetadata = $propertyInfoExtractor->getProperties(\get_class($entity));
        foreach ($entityMetadata as $key) {
            $setterMethod = 'set'.ucfirst($key);
            $getterMethod = 'get'.ucfirst($key);
            if (!method_exists($dto, $getterMethod)) {
                $getterMethod = 'is'.ucfirst($key);
                if (!method_exists($dto, $getterMethod)) {
                    continue;
                }
            }
            if (method_exists($entity, $setterMethod) && $key != 'id') {
                $value = $dto->$getterMethod();
                if (null !== $value || $setNullProperty) {
                    $entity->$setterMethod($value);
                }
            }
//            else {
//                throw new \BadMethodCallException("Method {$setterMethod} not found in Entity: ".\get_class($entityMetadata));
//            }
        }

        return $entity;
    }
}
