<?php

namespace App\Repository;

use App\Entity\User;
use App\Util\Factory\PropertyInfoExtractorFactory;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends EntityRepository implements UserLoaderInterface
{
    /**
     * UserRepository constructor.
     *
     * @param ManagerRegistry              $registry
     * @param PropertyInfoExtractorFactory $propertyInfoExtractorFactory
     */
    public function __construct(ManagerRegistry $registry, PropertyInfoExtractorFactory $propertyInfoExtractorFactory)
    {
        parent::__construct($registry, User::class, $propertyInfoExtractorFactory);
    }

    /**
     * Loads the user for the given username.
     *
     * This method must return null if the user is not found.
     *
     * @param string $username The username
     *
     * @return User|null
     *
     * @throws NonUniqueResultException
     */
    public function loadUserByUsername($username)
    {
        return $this->createQueryBuilder('u')
            ->where('u.nickname = :query OR u.email = :query')
            ->setParameter('query', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
