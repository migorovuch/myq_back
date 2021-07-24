<?php

namespace App\Model\DTO\User;

use App\Model\DTO\AbstractFindDTO;
use App\Model\DTO\PageDTO;
use DateTime;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class UserFindDTO extends AbstractFindDTO
{
    /**
     * @var int
     *
     * @Serializer\Type("integer")
     */
    protected $id;

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
     * @Assert\Type("string", groups={"Default"})
     * @Serializer\Type("string")
     */
    protected $nickname;

    /**
     * @var array
     *
     * @Assert\Choice(multiple=true, callback={"App\Model\Model\AbstractUser", "getRolesList"}, message="Wrong roles selected", groups={"Default"})
     * @Serializer\Type("array")
     */
    protected $roles;

    /**
     * @var int
     * @Serializer\Type("integer")
     */
    protected $status;

    /**
     * @var DateTime
     *
     * @Assert\Type(
     *     groups={"Default"},
     *     type="DateTime",
     *     message="The value {{ value }} is not a valid."
     * )
     * @Serializer\Type("DateTime<'Y-m-d H:i:s'>")
     */
    protected $dateCreate;

    /**
     * @var DateTime
     *
     * @Assert\Type(
     *     groups={"Default"},
     *     type="DateTime",
     *     message="The value {{ value }} is not a valid."
     * )
     * @Serializer\Type("DateTime<'Y-m-d H:i:s'>")
     */
    protected $dateUpdate;

    /**
     * UserFindDTO constructor.
     *
     * @param int      $id
     * @param string   $email
     * @param string   $nickname
     * @param array    $roles
     * @param int      $status
     * @param DateTime $dateCreate
     * @param DateTime $dateUpdate
     * @param string   $sort
     * @param PageDTO  $pageDTO
     * @param string   $condition
     */
    public function __construct(
        ?int $id,
        ?string $email,
        ?string $nickname,
        ?array $roles,
        ?int $status,
        ?DateTime $dateCreate,
        ?DateTime $dateUpdate,
        array $sort = null,
        PageDTO $pageDTO = null,
        string $condition = self::CONDITION_AND
    ) {
        parent::__construct($sort, $pageDTO ?? new PageDTO(), $condition);
        $this->id = $id;
        $this->email = $email;
        $this->nickname = $nickname;
        $this->roles = $roles;
        $this->status = $status;
        $this->dateCreate = $dateCreate;
        $this->dateUpdate = $dateUpdate;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    /**
     * @return array|null
     */
    public function getRoles(): ?array
    {
        return $this->roles;
    }

    /**
     * @return int|null
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @return DateTime|null
     */
    public function getDateCreate(): ?DateTime
    {
        return $this->dateCreate;
    }

    /**
     * @return DateTime|null
     */
    public function getDateUpdate(): ?DateTime
    {
        return $this->dateUpdate;
    }
}
