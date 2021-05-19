<?php

namespace App\Model\Manager;

use App\Model\DTO\DTOInterface;
use App\Model\DTO\AbstractFindDTO;
use App\Model\Model\EntityInterface;

/**
 * Interface CRUDManagerInterface.
 */
interface CRUDManagerInterface
{
    /**
     * @param string $id
     *
     * @return mixed
     */
    public function find(string $id);

    /**
     * @param AbstractFindDTO $data
     *
     * @return mixed
     */
    public function findByDTO(AbstractFindDTO $data);

    /**
     * @param array $criteria
     *
     * @return EntityInterface|null
     */
    public function findOneBy(array $criteria);

    /**
     * @return mixed
     */
    public function findAll();

    /**
     * @param $data
     *
     * @return mixed
     */
    public function create(DTOInterface $data);

    /**
     * @param string $id
     * @param $data
     *
     * @return mixed
     */
    public function update(string $id, DTOInterface $data);

    /**
     * @param string $id
     *
     * @return mixed
     */
    public function delete(string $id);
}
