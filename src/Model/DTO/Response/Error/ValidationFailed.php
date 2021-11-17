<?php

namespace App\Model\DTO\Response\Error;

use App\Model\DTO\DTOInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

/**
 * Class ValidationFailed.
 */
class ValidationFailed implements DTOInterface
{
    /**
     * @OA\Property(
     *      type="array",
     *      @OA\Items(
     *          type="object",
     *          ref=@Model(type=ValidationFieldFailed::class)
     *      ),
     *      description="Error fields"
     * )
     *
     * @var ValidationFieldFailed[]
     */
    private array $errors;

    /**
     * @param string                  $title
     * @param ValidationFieldFailed[] $errors
     */
    public function __construct(protected string $title, array $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return ValidationFieldFailed[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
