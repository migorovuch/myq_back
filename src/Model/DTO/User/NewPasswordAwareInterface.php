<?php

namespace App\Model\DTO\User;

interface NewPasswordAwareInterface
{
    /**
     * @return string|null
     */
    public function getNewPassword(): ?string;
}
