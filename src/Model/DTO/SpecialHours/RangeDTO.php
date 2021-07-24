<?php

namespace App\Model\DTO\SpecialHours;

use App\Model\DTO\DTOInterface;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class RangeDTO implements DTOInterface
{
    /**
     * @Assert\Type("string", groups={"Default"})
     * @Serializer\Type("string")
     */
    private ?string $from;

    /**
     * @Assert\Type("string", groups={"Default"})
     * @Serializer\Type("string")
     */
    private ?string $to;

    /**
     * RangeDTO constructor.
     */
    public function __construct(string $from, string $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * @return string|null
     */
    public function getFrom(): ?string
    {
        return $this->from;
    }

    /**
     * @return string|null
     */
    public function getTo(): ?string
    {
        return $this->to;
    }

    /**
     * @return null[]|string[]
     */
    public function toArray(): array
    {
        return [
            'from' => $this->getFrom(),
            'to' => $this->getTo(),
        ];
    }
}
