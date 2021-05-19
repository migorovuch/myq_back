<?php

namespace App\Exception;

use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Class ValidationFailedException.
 */
class ValidationFailedException extends RuntimeException implements ApiExceptionInterface
{
    /**
     * @var ConstraintViolationListInterface
     */
    private $list;

    /**
     * ValidationFailedException constructor.
     *
     * @param ConstraintViolationListInterface $list
     * @param string|null                      $message
     * @param int                              $code
     */
    public function __construct(ConstraintViolationListInterface $list, string $message = null, int $code = Response::HTTP_UNPROCESSABLE_ENTITY)
    {
        $message = $message ?? sprintf('Validation failed with %d error(s).', \count($list));
        parent::__construct($message, $code);

        $this->list = $list;
    }

    /**
     * @return ConstraintViolationListInterface
     */
    public function getConstraintViolationList()
    {
        return $this->list;
    }
}
