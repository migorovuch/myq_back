<?php

namespace App\Model\Manager;

interface SpecialHoursManagerInterface extends CRUDManagerInterface
{
    /**
     * @param array $list
     * @return array
     */
    public function updateList(array $list): array;
}
