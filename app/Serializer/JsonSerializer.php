<?php

declare(strict_types=1);

namespace App\Serializer;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateIntervalNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer as SymfonySerializer;

class JsonSerializer
{
    private SymfonySerializer $serializer;

    public function __construct()
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [
            new ArrayDenormalizer(),
            new ListDenormalizer(),
            new DateTimeNormalizer(),
            new DateIntervalNormalizer(),
            new ObjectNormalizer(),
        ];

        $this->serializer = new SymfonySerializer($normalizers, $encoders);
    }

    /**
     * @param array|object $data
     */
    public function serialize($data): string
    {
        return $this->serializer->serialize($data, JsonEncoder::FORMAT);
    }

    /**
     * @return object object of $targetClass type
     */
    public function deserialize(string $jsonContent, string $targetClass, array $context = [])
    {
        return $this->serializer->deserialize($jsonContent, $targetClass, JsonEncoder::FORMAT, $context);
    }

    /**
     * @param object|object[] $data object or array of objects for normalizing
     */
    public function normalize($data): array
    {
        return $this->serializer->normalize($data);
    }
}
