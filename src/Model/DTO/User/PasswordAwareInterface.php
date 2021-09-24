<?php

namespace App\Model\DTO\User;

interface PasswordAwareInterface
{
    /**
     * @return string|null
     */
    public function getPassword(): ?string;
}
