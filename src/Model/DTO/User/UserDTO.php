<?php

namespace App\Model\DTO\User;

use App\Model\DTO\DTOInterface;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class UserDTO implements DTOInterface
{
    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Type("string")
     * @Serializer\Type("string")
     */
    protected $nickname;

    /**
     * @var string
     *
     * @Assert\Email()
     * @Assert\Type("string")
     * @Serializer\Type("string")
     */
    protected $email;

    /**
     * @var string
     *
     * @Assert\NotBlank(groups={"googleAuthentication"})
     * @Assert\Type("string")
     * @Serializer\Type("string")
     */
    protected $googleTockenId;

    /**
     * @var string
     *
     * @Assert\Length(
     *     min="6",
     *     allowEmptyString=true,
     *     minMessage="Your password must be at least {{ limit }} characters long"
     * )
     * @Assert\Type("string")
     * @Assert\NotCompromisedPassword(
     *     message = "This password has previously appeared in a data breach. Please choose a more secure alternative."
     * )
     * @Assert\NotEqualTo(
     *     propertyPath = "email",
     *     message = "Your password should not be the same as your email."
     * )
     * @Assert\NotEqualTo(
     *     value = "Your application name",
     *     message = "Don't use the name of this application as your password."
     * )
     * @Serializer\Type("string")
     */
    protected $password;

    /**
     * @var array
     *
     * @Assert\NotNull()
     * @Assert\Choice(multiple=true, callback={"App\Entity\User", "getPublicRolesList"})
     * @Serializer\Type("array")
     */
    protected $roles;

    /**
     * RegistrationDTO constructor.
     *
     * @param string $nickname
     * @param string $email
     * @param string $password
     * @param array  $roles
     * @param string $googleTockenId
     */
    public function __construct(string $nickname, string $email, string $password, array $roles, string $googleTockenId)
    {
        $this->nickname = $nickname;
        $this->email = $email;
        $this->password = $password;
        $this->roles = $roles;
        $this->googleTockenId = $googleTockenId;
    }

    /**
     * @return string
     */
    public function getNickname(): string
    {
        return $this->nickname;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @return string|null
     */
    public function getGoogleTockenId(): ?string
    {
        return $this->googleTockenId;
    }
}
