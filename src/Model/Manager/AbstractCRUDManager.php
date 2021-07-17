<?php

namespace App\Model\Manager;

use App\Exception\AccessDeniedException;
use App\Model\DTO\DTOInterface;
use App\Model\DTO\AbstractFindDTO;
use App\Model\Model\EntityInterface;
use App\Repository\EntityRepositoryInterface;
use App\Security\AbstractVoter;
use App\Util\DTOExporter\DTOExporterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Class CRUDManager.
 */
abstract class AbstractCRUDManager
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var EntityRepositoryInterface
     */
    protected $entityRepository;

    /**
     * @var Security
     */
    protected $security;

    /**
     * @var DTOExporterInterface
     */
    protected $DTOExporter;

    /**
     * CRUDManager constructor.
     *
     * @param EntityManagerInterface    $entityManager
     * @param EntityRepositoryInterface $entityRepository
     * @param Security                  $security
     * @param DTOExporterInterface      $DTOExporter
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        EntityRepositoryInterface $entityRepository,
        Security $security,
        DTOExporterInterface $DTOExporter
    ) {
        $this->entityManager = $entityManager;
        $this->entityRepository = $entityRepository;
        $this->security = $security;
        $this->DTOExporter = $DTOExporter;
    }

    /**
     * @param string $id
     *
     * @return EntityInterface|null
     */
    public function find(string $id)
    {
        return $this->entityRepository->find($id);
    }

    /**
     * @return EntityInterface[]
     */
    public function findAll()
    {
        return $this->entityRepository->findAll();
    }

    /**
     * @param array $criteria
     *
     * @return EntityInterface|null
     */
    public function findOneBy(array $criteria)
    {
        return $this->entityRepository->findOneBy($criteria);
    }

    /**
     * @param AbstractFindDTO $data
     *
     * @return array
     */
    public function findByDTO(AbstractFindDTO $data)
    {
        return $this->entityRepository->findByDTO($data);
    }

    /**
     * @inheritDoc
     */
    public function countByDTO(AbstractFindDTO $data)
    {
        return $this->entityRepository->countByDTO($data);
    }

    /**
     * @param array      $criteria
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @return array
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->entityRepository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param string $id
     */
    public function delete(string $id)
    {
        $object = $this->find($id);
        $this->denyAccessUnlessGranted(AbstractVoter::DELETE, $object);
        $this->entityManager->remove($object);
        $this->entityManager->flush();
    }

    /**
     * @param $data
     */
    public function save($data)
    {
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    /**
     * @param mixed           $attributes
     * @param EntityInterface $subject
     *
     * @throws AccessDeniedException
     */
    public function denyAccessUnlessGranted($attributes, $subject = null)
    {
        if (!$this->security->isGranted($attributes, $subject)) {
            throw new AccessDeniedException();
        }
    }

    /**
     * @param DTOInterface $data
     *
     * @return EntityInterface
     */
    public function create(DTOInterface $data)
    {
        $entityName = $this->entityRepository->getClassName();
        $entity = new $entityName();
        $entity = $this->prepareEntity($entity, $data);
        $this->denyAccessUnlessGranted(AbstractVoter::CREATE, $entity);
        $this->save($entity);

        return $entity;
    }

    /**
     * @param string $id
     * @param DTOInterface $data
     *
     * @return EntityInterface
     */
    public function update(string $id, DTOInterface $data)
    {
        $entity = $this->find($id);
        $this->denyAccessUnlessGranted(AbstractVoter::UPDATE, $entity);
        $entity = $this->prepareEntity($entity, $data);
        $this->save($entity);

        return $entity;
    }

    /**
     * @param string $id
     * @param DTOInterface $data
     *
     * @return EntityInterface
     */
    public function change(string $id, DTOInterface $data): EntityInterface
    {
        $entity = $this->find($id);
        $this->denyAccessUnlessGranted(AbstractVoter::UPDATE, $entity);
        $entity = $this->prepareEntity($entity, $data, false);
        $this->save($entity);

        return $entity;
    }

    /**
     * @param EntityInterface $entity
     * @param DTOInterface $dto
     * @param bool $setNullProperty
     * @return EntityInterface
     */
    protected function prepareEntity(EntityInterface $entity, DTOInterface $dto, bool $setNullProperty = true): EntityInterface
    {
        return $this->DTOExporter->exportDTO($entity, $dto, $setNullProperty);
    }
}
