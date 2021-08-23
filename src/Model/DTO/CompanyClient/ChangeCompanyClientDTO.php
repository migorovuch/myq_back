<?php

namespace App\Model\DTO\CompanyClient;

use App\Entity\CompanyClient;
use App\Model\DTO\DTOInterface;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class ChangeCompanyClientDTO implements DTOInterface
{
    /**
     * @Assert\Type("int", groups={"Default"})
     * @Serializer\Type("integer")
     */
    protected ?int $status = null;

    /**
     * @Assert\Type("string", groups={"Default"})
     * @Serializer\Type("string")
     */
    protected ?string $pseudonym = null;

    /**
     * CompanyClientDTO constructor.
     *
     * @param int         $status
     * @param string|null $pseudonym
     */
    public function __construct(
        int $status = CompanyClient::STATUS_ON,
        string $pseudonym = null
    ) {
        $this->status = $status;
        $this->pseudonym = $pseudonym;
    }

    /**
     * @return int|null
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @return string|null
     */
    public function getPseudonym(): ?string
    {
        return $this->pseudonym;
    }
}
