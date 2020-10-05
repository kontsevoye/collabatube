<?php

declare(strict_types=1);

namespace App\Exception\Validator;

use App\Exception\AppExceptionInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidatorException extends \Exception implements AppExceptionInterface
{
    private ConstraintViolationListInterface $violationList;

    public function __construct(
        string $message,
        ConstraintViolationListInterface $violationList,
        int $code = 0,
        \Throwable $previous = null
    ) {
        $this->violationList = $violationList;
        parent::__construct($message, $code, $previous);
    }
}
