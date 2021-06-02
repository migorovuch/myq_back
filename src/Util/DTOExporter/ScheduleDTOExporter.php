<?php

namespace App\Util\DTOExporter;

use App\Entity\Schedule;
use App\Entity\User;
use App\Exception\UserHasNoCompanyException;
use App\Model\DTO\DTOInterface;
use App\Model\Model\EntityInterface;
use App\Util\Factory\PropertyInfoExtractorFactory;
use Symfony\Component\Security\Core\Security;

class ScheduleDTOExporter extends DTOExporter
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
        /** @var Schedule $entity */
        $entity = parent::exportDTO($entity, $dto, $setNullProperty);
        /** @var User $user */
        $user = $this->security->getUser();
        $company = $user->getFirstCompany();
        if (!$company) {
            throw new UserHasNoCompanyException();
        }
        return $entity->setCompany($company);
    }
}
