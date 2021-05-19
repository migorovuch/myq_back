<?php

namespace App\Util\DTOExporter;

use App\Model\DTO\DTOInterface;
use App\Model\Model\EntityInterface;

/**
 * Interface DTOExporterInterface.
 */
interface DTOExporterInterface
{
    /**
     * @param EntityInterface $entity
     * @param DTOInterface    $dto
     * @param bool            $setNullProperty
     *
     * @return EntityInterface
     */
    public function exportDTO(EntityInterface $entity, DTOInterface $dto, bool $setNullProperty = true): EntityInterface;
}
