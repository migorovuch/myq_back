<?php

namespace App\Model\DTO\User;

use App\Model\DTO\DTOInterface;
use App\Validator\ConstraintAccount;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ChangeUserDTO
 * @ConstraintAccount
 */
class ChangeUserDTO implements DTOInterface
{

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
    protected string $newPassword;

    /**
     * UpdateDTO constructor.
     * @param string $nickname
     * @param string $fullName
     * @param string $phone
     * @param string $password
     * @param string $newPassword
     */
    public function __construct(string $nickname, string $fullName, string $phone, string $password, string $newPassword)
    {
        $this->nickname = $nickname;
        $this->fullName = $fullName;
        $this->phone = $phone;
        $this->password = $password;
        $this->newPassword = $newPassword;
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
    public function getNewPassword(): string
    {
        return $this->newPassword;
    }
}
