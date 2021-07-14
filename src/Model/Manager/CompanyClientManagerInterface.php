<?php

namespace App\Model\Manager;

use App\Entity\CompanyClient;
use App\Model\DTO\CompanyClient\CompanyClientDTO;

interface CompanyClientManagerInterface extends CRUDManagerInterface
{
    /**
     * @param CompanyClientDTO $companyClientDTO
     * @return CompanyClient
     */
    public function getByDTO(CompanyClientDTO $companyClientDTO): CompanyClient;
}
