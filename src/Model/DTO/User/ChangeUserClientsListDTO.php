<?php

namespace App\Model\DTO\User;

use App\Model\DTO\DTOInterface;
use JMS\Serializer\Annotation as Serializer;
use OpenApi\Annotations as OA;

class ChangeUserClientsListDTO implements DTOInterface
{
    /**
     * @Serializer\Type("array<string>")
     * @OA\Property(
     *      type="array",
     *      @OA\Items(
     *          type="string",
     *      ),
     *      description="Clints id's"
     * )
     */
    protected ?array $clients = null;

    /**
     * ChangeUserClientsListDTO constructor.
     *
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
