<?php

declare(strict_types=1);

namespace App\Validator;

use App\Exception\Validator\ValidatorException;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Adapter\Psr16Adapter;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AnnotationValidator
{
    private ValidatorInterface $validator;

    public function __construct(CacheInterface $cache)
    {
        $this->validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->setMappingCache(new Psr16Adapter($cache))
            ->getValidator();
    }

    /**
     * @throws ValidatorException
     */
    public function validate(ValidatorAwareInterface $data): void
    {
        /** @var ConstraintViolationList $list */
        $list = $this->validator->validate($data);
        if ($list->count() > 0) {
            throw new ValidatorException($this->transformConstraintListToString($list), $list);
        }
    }

    private function transformConstraintListToString(ConstraintViolationList $list): string
    {
        return trim(array_reduce(
            iterator_to_array($list->getIterator()),
            function (string $carry, ConstraintViolationInterface $violation) {
                $message = substr($violation->getMessage(), 0, -1);
                return "{$carry}{$violation->getPropertyPath()}: {$message}; ";
            },
            ''
        ));
    }
}
