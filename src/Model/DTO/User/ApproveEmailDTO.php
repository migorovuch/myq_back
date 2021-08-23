<?php

namespace App\Model\DTO\User;

use App\Model\DTO\DTOInterface;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class ApproveEmailDTO implements DTOInterface
{
    /**
     * @var string
     *
     * @Assert\NotBlank(groups={"Default"}, message="This value should not be blank")
     * @Assert\Type("string", groups={"Default"})
     * @Serializer\Type("string")
     */
    protected ?string $token = null;

    /**
     * @var string
     *
     * @Assert\NotBlank(groups={"Default"}, message="This value should not be blank")
     * @Assert\Type("string", groups={"Default"})
     * @Serializer\Type("string")
     */
    protected ?string $id = null;

    /**
     * ApproveEmailDTO constructor.
     *
     * @param string|null $token
     * @param string|null $id
     */
    public function __construct(string $token = null, string $id = null)
    {
        $this->token = $token;
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @return string
     */
    public function getId(): ?string
    {
        return $this->id;
    }
}
