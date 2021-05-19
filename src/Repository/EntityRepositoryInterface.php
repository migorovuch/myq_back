<?php

namespace App\Repository;

use App\Model\DTO\AbstractFindDTO;
use App\Model\Model\EntityInterface;
use Doctrine\Persistence\ObjectRepository;

/**
 * Interface EntityRepositoryInterface.
 */
interface EntityRepositoryInterface extends ObjectRepository
{
    /**
     * @param AbstractFindDTO $data
     *
     * @return EntityInterface[]
     */
    public function findByDTO(AbstractFindDTO $data);

    /**
     * @param AbstractFindDTO $data
     *
     * @return int
     */
    public function countByDTO(AbstractFindDTO $data);
}
