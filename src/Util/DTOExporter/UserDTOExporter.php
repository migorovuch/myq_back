<?php

namespace App\Util\DTOExporter;

use App\Entity\User;
use App\Model\DTO\DTOInterface;
use App\Model\Model\EntityInterface;
use App\Util\Factory\PropertyInfoExtractorFactory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

class UserDTOExporter extends DTOExporter
{
    /**
     * @var Security
     */
    protected $security;
    /**
     * @var UserPasswordEncoderInterface
     */
    protected $userPasswordEncoder;

    /**
     * UserDTOExporter constructor.
     *
     * @param PropertyInfoExtractorFactory $propertyInfoExtractorFactory
     * @param Security                     $security
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     */
    public function __construct(
        PropertyInfoExtractorFactory $propertyInfoExtractorFactory,
        Security $security,
        UserPasswordEncoderInterface $userPasswordEncoder
    ) {
        parent::__construct($propertyInfoExtractorFactory);
        $this->security = $security;
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    /**
     * @param User         $entity
     * @param DTOInterface $dto
     * @param bool         $setNullProperty
     *
     * @return User
     */
    public function exportDTO(
        EntityInterface $entity,
        DTOInterface $dto,
        bool $setNullProperty = true
    ): EntityInterface {
        $oldStatus = $entity->getStatus();
        /** @var User $entity */
        $entity = parent::exportDTO($entity, $dto, $setNullProperty);
        $entity->setPassword($this->userPasswordEncoder->encodePassword($entity, $entity->getPassword()));
        if (!$this->security->isGranted(User::ROLE_ADMIN)) {
            $entity->setStatus($oldStatus);
        }

        return $entity;
    }
}
