<?php

namespace App\Repository;

use App\Entity\Company;
use App\Model\DTO\AbstractFindDTO;
use App\Util\Factory\PropertyInfoExtractorFactory;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Company|null find($id, $lockMode = null, $lockVersion = null)
 * @method Company|null findOneBy(array $criteria, array $orderBy = null)
 * @method Company[]    findAll()
 * @method Company[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry, PropertyInfoExtractorFactory $propertyInfoExtractorFactory)
    {
        parent::__construct($registry, Company::class, $propertyInfoExtractorFactory);
    }

    /**
     * @param string      $slug
     * @param string|null $exceptId
     *
     * @return Company|null
     *
     * @throws NonUniqueResultException
     */
    public function findBySlug(string $slug, string $exceptId = null): ?Company
    {
        $slug = strtolower($slug);
        $queryBuilder = $this->createQueryBuilder('c')
            ->where('c.slug = :slug')
            ->orWhere('c.id = :slug')
            ->setParameter('slug', $slug);
        if ($exceptId) {
            $queryBuilder->andWhere('c.id != :id')->setParameter(':id', $exceptId);
        }

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    public function findPublicByDTO(AbstractFindDTO $data)
    {
        $qb = $this->createQueryBuilder('t');
        $qb = $this->buildQueryByDTO($qb, $data);
        $qb->andWhere('t.status=:status')->setParameter('status', Company::STATUS_ON);
        $qb = $this->paginationQueryByDTO($qb, $data);
        $query = $qb->getQuery();

        return $query->getResult();
    }
}
