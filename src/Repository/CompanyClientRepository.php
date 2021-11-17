<?php

namespace App\Repository;

use App\Entity\CompanyClient;
use App\Entity\User;
use App\Model\DTO\AbstractFindDTO;
use App\Model\DTO\CompanyClient\CompanyClientFindDTO;
use App\Util\Factory\PropertyInfoExtractorFactory;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CompanyClient|null find($id, $lockMode = null, $lockVersion = null)
 * @method CompanyClient|null findOneBy(array $criteria, array $orderBy = null)
 * @method CompanyClient[]    findAll()
 * @method CompanyClient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyClientRepository extends EntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        PropertyInfoExtractorFactory $propertyInfoExtractorFactory = null
    ) {
        parent::__construct($registry, CompanyClient::class, $propertyInfoExtractorFactory);
    }

    /**
     * @param CompanyClientFindDTO $data
     *
     * @return array
     */
    public function findByDTO(AbstractFindDTO $data)
    {
        $qb = $this->createQueryBuilder('t');
        $qb = $this->buildQueryByDTO($qb, $data)
            ->leftJoin('t.bookings', 'b')
            ->addSelect('COUNT(b.id) as numberOfBookings')
            ->groupBy('t.id');
        if ($data->getCompanyName()) {
            $qb
                ->innerJoin('t.company', 'c')
                ->andWhere('c.name LIKE :companyName')
                ->setParameter('companyName', $data->getCompanyName());
        }
        if (null !== $data->getStatus()) {
            $qb
                ->andWhere('t.status = :status')
                ->setParameter('status', $data->getStatus());
        }
        if ($data->getName()) {
            $qb
                ->orWhere('t.pseudonym LIKE :pseudonym')
                ->setParameter('pseudonym', $data->getName());
        }
        if (null !== $data->getDeleted()) {
            $qb
                ->andWhere('t.deleted = :deleted')
                ->setParameter('deleted', $data->getDeleted());
        }
        $qb = $this->paginationQueryByDTO($qb, $data);
        $query = $qb->getQuery();

        $searchResult = $query->getResult();
        $clients = [];
        foreach ($searchResult as $companyClient) {
            $clients[] = ($companyClient[0])->setNumberOfBookings($companyClient['numberOfBookings']);
        }

        return $clients;
    }

    /**
     * @param User $user
     *
     * @return int|mixed|string
     */
    public function changeClientDetails(User $user)
    {
        $query = $this->createQueryBuilder('')
            ->update(CompanyClient::class, 'cc')

            ->set('cc.name', ':fullName')
            ->setParameter('fullName', $user->getFullName())
            ->set('cc.phone', ':phone')
            ->setParameter('phone', $user->getPhone())

            ->where('cc.user = :user')
            ->setParameter('user', $user)
            ->getQuery();

        return $query->execute();
    }

    public function getListByIDsWithoutUser(array $ids): array
    {
        $qb = $this->createQueryBuilder('t');

        return $qb->andWhere($qb->expr()->in('t.id', $ids))
            ->andWhere('t.user IS NULL')
            ->getQuery()
            ->getResult();
    }
}
