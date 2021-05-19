<?php

namespace App\Model\DTO\User;

use App\Model\DTO\DTOInterface;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ResetPasswordDTO.
 */
class ResetPasswordDTO implements DTOInterface
{
    /**
     * @var string
     *
     * @Assert\Email()
     * @Assert\Type("string")
     * @Serializer\Type("string")
     */
    protected $email;

    /**
     * ResetPasswordDTO constructor.
     *
     * @param string $email
     */
    public function __construct(string $email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }
}
