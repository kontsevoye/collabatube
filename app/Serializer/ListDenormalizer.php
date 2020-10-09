<?php

namespace App\Serializer;

use Symfony\Component\Serializer\Exception\BadMethodCallException;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @see ArrayDenormalizer
 */
class ListDenormalizer implements ContextAwareDenormalizerInterface, SerializerAwareInterface, CacheableSupportsMethodInterface
{
    public const ASSOCIATION_CONTEXT_KEY = 'LIST_ASSOCIATION';

    /**
     * @var SerializerInterface|DenormalizerInterface
     */
    private $serializer;

    /**
     * {@inheritdoc}
     *
     * @throws NotNormalizableValueException
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        if (null === $this->serializer) {
            throw new BadMethodCallException('Please set a serializer before calling denormalize()!');
        }
        if (!\is_array($data)) {
            throw new InvalidArgumentException('Data expected to be an array, '.get_debug_type($data).' given.');
        }
        $itemType = $this->getItemType($type, $context);
        if ('' === $itemType) {
            throw new InvalidArgumentException('Unsupported class: '.$type);
        }

        $serializer = $this->serializer;

        $builtinType = isset($context['key_type']) ? $context['key_type']->getBuiltinType() : null;
        foreach ($data as $key => $value) {
            if (null !== $builtinType && !('is_'.$builtinType)($key)) {
                throw new NotNormalizableValueException(sprintf('The type of the key "%s" must be "%s" ("%s" given).', $key, $builtinType, get_debug_type($key)));
            }

            $data[$key] = $serializer->denormalize($value, $itemType, $format, $context);
        }
        $data = new $type($data);

        return $data;
    }

    private function getItemType(string $type, array $context): string
    {
        if (!array_key_exists(self::ASSOCIATION_CONTEXT_KEY, $context)) {
            return '';
        }

        if (!array_key_exists($type, $context[self::ASSOCIATION_CONTEXT_KEY])) {
            return '';
        }

        return $context[self::ASSOCIATION_CONTEXT_KEY][$type];
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, string $type, string $format = null, array $context = []): bool
    {
        if (null === $this->serializer) {
            throw new BadMethodCallException(sprintf('The serializer needs to be set to allow "%s()" to be used.', __METHOD__));
        }

        if (!is_array($data)) {
            return false;
        }

        if ($this->getItemType($type, $context) === '') {
            return false;
        }

        $r = new \ReflectionClass($type);

        return $r->getConstructor()->getNumberOfParameters() === 1;
    }

    /**
     * {@inheritdoc}
     */
    public function setSerializer(SerializerInterface $serializer)
    {
        if (!$serializer instanceof DenormalizerInterface) {
            throw new InvalidArgumentException('Expected a serializer that also implements DenormalizerInterface.');
        }

        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function hasCacheableSupportsMethod(): bool
    {
        return $this->serializer instanceof CacheableSupportsMethodInterface
            && $this->serializer->hasCacheableSupportsMethod();
    }
}
