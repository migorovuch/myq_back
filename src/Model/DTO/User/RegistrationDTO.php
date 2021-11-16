<?php

namespace App\Model\DTO\User;

use App\Model\DTO\DTOInterface;
use App\Validator\ConstraintAccountUniqueEmail;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Annotations as OA;

/**
 * Class RegistrationDTO.
 *
 * @ConstraintAccountUniqueEmail
 */
class RegistrationDTO implements DTOInterface, PasswordAwareInterface
{
    /**
     * @var string
     *
     * @Assert\NotBlank(groups={"nickname"}, message="This value should not be blank")
     * @Assert\Type("string", groups={"Default"})
     * @Serializer\Type("string")
     */
    protected $nickname;
    /**
     * @var string
     *
     * @Assert\NotBlank(groups={"Default"}, message="This value should not be blank")
     * @Assert\Type("string", groups={"Default"})
     * @Serializer\Type("string")
     */
    protected $fullName;

    /**
     * @var string
     *
     * @Assert\Email(groups={"Default"}, message="Invalid email format")
     * @Assert\Type("string", groups={"Default"})
     * @Serializer\Type("string")
     */
    protected $email;

    /**
     * @var string
     *
     * @Assert\NotBlank(groups={"googleAuthentication"})
     * @Assert\Type("string", groups={"Default"})
     * @Serializer\Type("string")
     */
    protected $googleTockenId;

    /**
     * @var string
     *
     * @Assert\NotBlank(groups={"Default"}, message="This value should not be blank")
     * @Assert\Length(
     *     min="6",
     *     groups={"Default"},
     *     allowEmptyString=true,
     *     minMessage="Your password must be at least {{ limit }} characters long"
     * )
     * @Assert\Type("string", groups={"Default"})
     * @Assert\NotEqualTo(
     *     groups={"Default"},
     *     propertyPath = "email",
     *     message = "Your password should not be the same as your email."
     * )
     * @Assert\NotEqualTo(
     *     groups={"Default"},
     *     value = "myqpassword",
     *     message = "Don't use the name of this application as your password."
     * )
     * @Serializer\Type("string")
     */
    protected $password;

    /**
     * @var array
     *
     * @Assert\NotNull(groups={"Default"}, message="This value should not be blank")
     * @Assert\Choice(multiple=true, callback={"App\Entity\User", "getPublicRolesList"}, message="Wrong roles selected", groups={"Default"})
     * @Serializer\Type("array")
     * @OA\Property(type="array", @OA\Items(type="string"), description="User roles")
     */
    protected $roles;

    /**
     * RegistrationDTO constructor.
     *
     * @param string $nickname
     * @param string $fullName
     * @param string $email
     * @param string $password
     * @param array  $roles
     * @param string $googleTockenId
     */
    public function __construct(string $nickname, string $fullName, string $email, string $password, array $roles, string $googleTockenId)
    {
        $this->nickname = $nickname;
        $this->email = $email;
        $this->password = $password;
        $this->roles = $roles;
        $this->googleTockenId = $googleTockenId;
        $this->fullName = $fullName;
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
    public function getFullName(): string
    {
        return $this->fullName;
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
