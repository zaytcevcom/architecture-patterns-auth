<?php

declare(strict_types=1);

namespace App\Components\Serializer;

use ArrayObject;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final readonly class Normalizer
{
    public function __construct(
        private NormalizerInterface $normalizer
    ) {}

    /** @throws ExceptionInterface */
    public function normalize(mixed $object): null|array|ArrayObject|bool|float|int|string
    {
        return $this->normalizer->normalize($object);
    }
}
