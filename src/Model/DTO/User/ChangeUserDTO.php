<?php


namespace App\Model\DTO\User;

use App\Entity\User;
use App\Model\DTO\DTOInterface;
use App\Validator\ConstraintAccount;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\ConstraintAccountUniqueEmail;

/**
 * Class ChangeUserDTO
 * @ConstraintAccountUniqueEmail
 */
class ChangeUserDTO implements DTOInterface, NewPasswordAwareInterface
{

    /**
     * @var string
     *
     * @Assert\NotBlank(groups={"Default"}, message="This value should not be blank")
     * @Assert\Type("string", groups={"Default"})
     * @Serializer\Type("string")
     */
    protected string $id;

    /**
     * @var string
     *
     * @Assert\NotBlank(groups={"Default"}, message="This value should not be blank")
     * @Assert\Type("string", groups={"Default"})
     * @Serializer\Type("string")
     */
    protected string $nickname;
    /**
     * @var string
     *
     * @Assert\NotBlank(groups={"Default"}, message="This value should not be blank")
     * @Assert\Type("string", groups={"Default"})
     * @Serializer\Type("string")
     */
    protected string $fullName;
    /**
     * @var string
     *
     * @Assert\NotBlank(groups={"Default"}, message="This value should not be blank")
     * @Assert\Type("string", groups={"Default"})
     * @Serializer\Type("string")
     */
    protected string $phone;

    /**
     * @var string
     *
     * @Assert\Length(
     *     min="6",
     *     groups={"Default"},
     *     allowEmptyString=true,
     *     minMessage="Your password must be at least {{ limit }} characters long"
     * )
     * @Assert\Type("string", groups={"Default"})
     * @Assert\NotEqualTo(
     *     groups={"Default"},
     *     value = "myqpassword",
     *     message = "Don't use the name of this application as your password."
     * )
     * @Serializer\Type("string")
     */
    protected ?string $newPassword = null;

    /**
     * @var string
     *
     * @Assert\Email(groups={"Default"}, message="Invalid email format")
     * @Assert\Type("string", groups={"Default"})
     * @Serializer\Type("string")
     */
    protected $email;

    /**
     * @Assert\Type("int", groups={"Default"})
     * @Assert\Choice(choices=App\Entity\User::STATUS_LIST, message="Wrong status selected", groups={"Default"})
     * @Serializer\Type("integer")
     * @var int|null
     */
    protected ?int $status = User::STATUS_OFF;

    /**
     * @var array
     *
     * @Assert\NotNull(groups={"Default"}, message="This value should not be blank")
     * @Assert\Choice(multiple=true, callback={"App\Entity\User", "getRolesList"}, message="Wrong roles selected", groups={"Default"})
     * @Serializer\Type("array")
     */
    protected array $roles;

    /**
     * ChangeUserDTO constructor.
     * @param string $id
     * @param string $nickname
     * @param string $fullName
     * @param string $phone
     * @param string $newPassword
     * @param string $email
     * @param int $status
     * @param array $roles
     */
    public function __construct(string $id, string $nickname, string $fullName, string $phone, string $newPassword, string $email, int $status, array $roles)
    {

        $this->status = $status;
        $this->nickname = $nickname;
        $this->fullName = $fullName;
        $this->phone = $phone;
        $this->newPassword = $newPassword;
        $this->email = $email;
        $this->id = $id;
        $this->roles = $roles;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
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
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @return string|null
     */
    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return int|null
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }
}
