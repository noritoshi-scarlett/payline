<?php
declare(strict_types=1);

namespace Noritoshi\Payline\Application\Utility\Normalizer;

class CollectionNormalizer
{
    public static function toArray(iterable $iterable): array
    {
        return is_array($iterable) ? $iterable : iterator_to_array($iterable);
    }

    public static function toIterable(array $array): iterable
    {
        foreach ($array as $value) {
            yield $value;
        }
    }
}