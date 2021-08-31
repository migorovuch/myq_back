<?php

namespace App\Model\DTO\User;

use App\Model\DTO\DTOInterface;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class ChangeUserClientsListDTO implements DTOInterface
{
    /**
     * @Assert\NotBlank(groups={"Default"}, message="This value should not be blank")
     * @Serializer\Type("array<string>")
     */
    protected ?array $clients = null;

    /**
     * ChangeUserClientsListDTO constructor.
     * @param array|null $clients
     */
    public function __construct(?array $clients)
    {
        $this->clients = $clients;
    }

    /**
     * @return array|null
     */
    public function getClients(): ?array
    {
        return $this->clients;
    }
}
