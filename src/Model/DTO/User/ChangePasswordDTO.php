<?php

namespace App\Model\DTO\User;

use App\Model\DTO\DTOInterface;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ChangePasswordDTO.
 */
class ChangePasswordDTO implements DTOInterface
{
    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Length(
     *     min="6",
     *     minMessage="Your password must be at least {{ limit }} characters long"
     * )
     * @Assert\Type("string")
     * @Serializer\Type("string")
     */
    protected $password;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Type("string")
     * @Serializer\Type("string")
     */
    protected $token;

    /**
     * ChangePasswordDTO constructor.
     *
     * @param string $password
     * @param string $token
     */
    public function __construct(string $password, string $token)
    {
        $this->password = $password;
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }
}
