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
 * @ConstraintAccount
 */
class ChangeUserDTO implements DTOInterface
{

    /**
     * @var string
     *
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
     * @Assert\Type("string", groups={"Default"})
     * @Assert\NotEqualTo(
     *     groups={"Default"},
     *     value = "myqpassword",
     *     message = "Don't use the name of this application as your password."
     * )
     * @Serializer\Type("string")
     */
    protected string $password;

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
     * ChangeUserDTO constructor.
     * @param string $id
     * @param string $nickname
     * @param string $fullName
     * @param string $phone
     * @param string $password
     * @param string $email
     * @param int $status
     */
    public function __construct(string $id, string $nickname, string $fullName, string $phone, string $password, string $email, int $status)
    {

        $this->status = $status;
        $this->nickname = $nickname;
        $this->fullName = $fullName;
        $this->phone = $phone;
        $this->password = $password;
        $this->email = $email;
        $this->id = $id;
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
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
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
}
