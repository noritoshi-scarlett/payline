<?php
declare(strict_types=1);

namespace Payline\App\Application\Library\Normalizer;

class CollectionNormalizer
{
    public static function toArray(iterable $iterable): array
    {
        return iterator_to_array($iterable);
    }

    public static function toIterable(array $array): iterable
    {
        foreach ($array as $key => $value) {
            yield $value;
        }
    }
}