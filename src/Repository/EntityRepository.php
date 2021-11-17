<?php

namespace App\Repository;

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
     * @param ManagerRegistry                   $registry
     * @param string                            $entityClass
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
     * @param Criteria        $criteria
     * @param AbstractFindDTO $data
     *
     * @return Criteria
     */
    protected function buildCriteriaByDTO(Criteria $criteria, AbstractFindDTO $data)
    {
        if ($data->getPage()) {
            $criteria
                ->setMaxResults($data->getPage()->getLimit())
                ->setFirstResult($data->getPage()->getOffset());
        }
        if (!empty($data->getSort())) {
            $criteria->orderBy($data->getSort());
        }
        $propertyInfoExtractor = $this->propertyInfoExtractorFactory->buildPropertyInfoExtractor();
        $abstractClassMetadata = $propertyInfoExtractor->getProperties(AbstractFindDTO::class);
        $entityProperties = $propertyInfoExtractor->getProperties($this->getEntityName());
        $findFields = array_diff($entityProperties, $abstractClassMetadata);

        $propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->enableExceptionOnInvalidIndex()
            ->getPropertyAccessor();
        $expr = [];
        foreach ($findFields as $key) {
            if (
                $propertyAccessor->isReadable($data, $key) &&
                ($value = $propertyAccessor->getValue($data, $key)) &&
                \in_array($key, $entityProperties)
            ) {
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
     * @param QueryBuilder    $queryBuilder
     * @param AbstractFindDTO $data
     *
     * @return QueryBuilder
     */
    protected function paginationQueryByDTO(QueryBuilder $queryBuilder, AbstractFindDTO $data): QueryBuilder
    {
        if ($data->getPage()) {
            $queryBuilder
                ->setMaxResults($data->getPage()->getLimit())
                ->setFirstResult($data->getPage()->getOffset());
        }
        if (!empty($data->getSort())) {
            foreach ($data->getSort() as $key => $val) {
                $key = lcfirst(implode('', array_map('ucfirst', explode('_', $key))));
                $queryBuilder->addOrderBy('t.'.$key, $val);
            }
        }

        return $queryBuilder;
    }

    /**
     * @param QueryBuilder    $queryBuilder
     * @param AbstractFindDTO $data
     *
     * @return QueryBuilder
     */
    protected function buildQueryByDTO(QueryBuilder $queryBuilder, AbstractFindDTO $data): QueryBuilder
    {
        $propertyInfoExtractor = $this->propertyInfoExtractorFactory->buildPropertyInfoExtractor();
        $abstractClassMetadata = $propertyInfoExtractor->getProperties(AbstractFindDTO::class);
        $entityProperties = $propertyInfoExtractor->getProperties($this->getEntityName());
        $dataProperties = $propertyInfoExtractor->getProperties(\get_class($data));
        $findFields = array_diff($entityProperties, $abstractClassMetadata);

        $propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->enableExceptionOnInvalidIndex()
            ->getPropertyAccessor();
        $rootAliases = $queryBuilder->getRootAliases();
        $entityAlias = reset($rootAliases);
        $orStatements = $queryBuilder->expr()->orX();
        foreach ($findFields as $key) {
            if (
                \in_array($key, $dataProperties) &&
                \in_array($key, $entityProperties) &&
                ($value = $propertyAccessor->getValue($data, $key))
            ) {
                $expression = $queryBuilder->expr()->like($entityAlias.'.'.$key, ':q'.$key);
                if (\is_array($value)) {
                    $expression = $queryBuilder->expr()->in($entityAlias.'.'.$key, ':q'.$key);
                } elseif ($value instanceof EntityInterface) {
                    $expression = $queryBuilder->expr()->eq($entityAlias.'.'.$key, ':q'.$key);
                }
                if (AbstractFindDTO::CONDITION_AND === $data->getCondition()) {
                    $queryBuilder->andWhere($expression);
                } elseif (AbstractFindDTO::CONDITION_OR === $data->getCondition()) {
                    $orStatements->add($expression);
                }
                $queryBuilder->setParameter(':q'.$key, $value);
            }
        }
        if ($orStatements->count()) {
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
        $qb = $this->createQueryBuilder('t');
        $qb = $this->buildQueryByDTO($qb, $data);
        $qb = $this->paginationQueryByDTO($qb, $data);
        $query = $qb->getQuery();

        return $query->getResult();
    }

    /**
     * {@inheritDoc}
     */
    public function countByDTO(AbstractFindDTO $data)
    {
        $qb = $this->createQueryBuilder('t');
        $qb->select('count(t.id)');
        $qb = $this->buildQueryByDTO($qb, $data);

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getListByIDs(array $ids): array
    {
        $qb = $this->createQueryBuilder('t');

        return $qb->andWhere($qb->expr()->in('t.id', $ids))
            ->getQuery()
            ->getResult();
    }
}
