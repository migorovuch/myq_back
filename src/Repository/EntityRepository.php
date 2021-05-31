<?php

namespace App\Repository;

use App\Model\DTO\DTOInterface;
use App\Model\DTO\AbstractFindDTO;
use App\Model\Model\EntityInterface;
use App\Util\Factory\PropertyInfoExtractorFactory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Class EntityRepository.
 */
class EntityRepository extends ServiceEntityRepository implements EntityRepositoryInterface
{
    /**
     * @var PropertyInfoExtractorFactory
     */
    protected $propertyInfoExtractorFactory;

    /**
     * EntityRepository constructor.
     *
     * @param ManagerRegistry $registry
     * @param string $entityClass
     * @param PropertyInfoExtractorFactory|null $propertyInfoExtractorFactory
     */
    public function __construct(
        ManagerRegistry $registry,
        string $entityClass = '',
        PropertyInfoExtractorFactory $propertyInfoExtractorFactory = null
    ) {
        parent::__construct($registry, $entityClass);
        $this->propertyInfoExtractorFactory = $propertyInfoExtractorFactory;
    }

    /**
     * @param Criteria     $criteria
     * @param AbstractFindDTO $data
     *
     * @return Criteria
     */
    protected function buildCriteriaByDTO(Criteria $criteria, AbstractFindDTO $data)
    {
        $criteria
            ->setMaxResults($data->getPage()->getLimit())
            ->setFirstResult($data->getPage()->getOffset());
        if ($data->getSort()) {
            $criteria->orderBy(explode(',', $data->getSort()));
        }
        $propertyInfoExtractor = $this->propertyInfoExtractorFactory->buildPropertyInfoExtractor();
        $abstractClassMetadata = $propertyInfoExtractor->getProperties(AbstractFindDTO::class);
        $entityProperties = $propertyInfoExtractor->getProperties(\get_class($data));
        $findFields = array_diff($entityProperties, $abstractClassMetadata);

        $propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->enableExceptionOnInvalidIndex()
            ->getPropertyAccessor();
        $expr = [];
        foreach ($findFields as $key) {
            if (($value = $propertyAccessor->getValue($data, $key)) && \in_array($key, $entityProperties)) {
                if (\is_array($value)) {
                    $criteria->andWhere($criteria->expr()->in($key, $value));
                } else {
                    $criteria->andWhere($criteria->expr()->eq($key, $value));
                    $expr[$key] = $value;
                }
            }
        }

        return $criteria;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param AbstractFindDTO $data
     *
     * @return QueryBuilder
     */
    protected function buildQueryByDTO(QueryBuilder $queryBuilder, AbstractFindDTO $data): QueryBuilder
    {
        $queryBuilder
            ->setMaxResults($data->getPage()->getLimit())
            ->setFirstResult($data->getPage()->getOffset());
        if ($data->getSort()) {
            $queryBuilder->orderBy(explode(',', $data->getSort()));
        }
        $propertyInfoExtractor = $this->propertyInfoExtractorFactory->buildPropertyInfoExtractor();
        $abstractClassMetadata = $propertyInfoExtractor->getProperties(AbstractFindDTO::class);
        $entityProperties = $propertyInfoExtractor->getProperties(\get_class($data));
        $findFields = array_diff($entityProperties, $abstractClassMetadata);

        $propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->enableExceptionOnInvalidIndex()
            ->getPropertyAccessor();
        $rootAliases = $queryBuilder->getRootAliases();
        $entityAlias = reset($rootAliases);
        $orStatements = $queryBuilder->expr()->orX();
        foreach ($findFields as $key) {
            if (($value = $propertyAccessor->getValue($data, $key)) && \in_array($key, $entityProperties)) {
                if (\is_array($value)) {
                    if ($data->getCondition() === AbstractFindDTO::CONDITION_AND) {
                        $queryBuilder->andWhere($queryBuilder->expr()->in($entityAlias.'.'.$key, ':q'.$key));
                    } elseif ($data->getCondition() === AbstractFindDTO::CONDITION_OR) {
                        $orStatements->add($queryBuilder->expr()->in($entityAlias.'.'.$key, ':q'.$key));
                    }
                } else {
                    if ($data->getCondition() === AbstractFindDTO::CONDITION_AND) {
                        $queryBuilder->andWhere($queryBuilder->expr()->like($entityAlias.'.'.$key, ':q'.$key));
                    } elseif ($data->getCondition() === AbstractFindDTO::CONDITION_OR) {
                        $orStatements->add($queryBuilder->expr()->like($entityAlias.'.'.$key, ':q'.$key));
                    }
                }
                $queryBuilder->setParameter(':q'.$key, $value);
            }
        }
        if($orStatements->count()) {
            $queryBuilder->andWhere($orStatements);
        }

        return $queryBuilder;
    }

    /**
     * @param AbstractFindDTO $data
     *
     * @return EntityInterface[]
     */
    public function findByDTO(AbstractFindDTO $data)
    {
        $criteria = new Criteria();
        $criteria = $this->buildCriteriaByDTO($criteria, $data);

        return $this->matching($criteria)->toArray();
    }

    /**
     * @inheritDoc
     */
    public function countByDTO(AbstractFindDTO $data)
    {
        $criteria = new Criteria();
        $criteria = $this->buildCriteriaByDTO($criteria, $data);

        return $this->_em->getUnitOfWork()->getEntityPersister($this->_entityName)->count($criteria);
    }
}
