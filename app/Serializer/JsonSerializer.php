<?php

declare(strict_types=1);

namespace App\Serializer;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer as SymfonySerializer;

class JsonSerializer
{
    private SymfonySerializer $serializer;

    public function __construct()
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

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
    public function deserialize(string $jsonContent, string $targetClass)
    {
        return $this->serializer->deserialize($jsonContent, $targetClass, JsonEncoder::FORMAT);
    }

    /**
     * @param object $data
     */
    public function normalize($data): array
    {
        return $this->serializer->normalize($data);
    }
}
