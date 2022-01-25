<?php

namespace App\Model\Manager;

interface CompanyManagerInterface extends CRUDManagerInterface
{
    /**
     * @param string      $slug
     * @param string|null $exceptId
     *
     * @return bool
     */
    public function isSlugExists(string $slug, string $exceptId = null): bool;
}
