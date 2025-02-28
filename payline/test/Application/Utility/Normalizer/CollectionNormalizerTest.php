<?php
declare(strict_types=1);

namespace Noritoshi\Payline\Test\Application\Utility\Normalizer;

use PHPUnit\Framework\TestCase;
use Noritoshi\Payline\Application\Utility\Normalizer\CollectionNormalizer;

class CollectionNormalizerTest extends TestCase
{
    public function testToArrayWithGenerator(): void
    {
        $generator = (function () {
            yield 'item1';
            yield 'item2';
            yield 'item3';
        })();
        $result = CollectionNormalizer::toArray($generator);

        $this->assertEquals(['item1', 'item2', 'item3'], $result);
    }

    public function testToArrayWithArray(): void
    {
        $array = ['item1', 'item2', 'item3'];
        $result = CollectionNormalizer::toArray($array);

        $this->assertEquals($array, $result);
    }

    public function testToIterable(): void
    {
        $array = ['item1', 'item2', 'item3'];

        $iterable = CollectionNormalizer::toIterable($array);

        $this->assertIsIterable($iterable);
        foreach ($iterable as $i => $item) {
            $this->assertSame($item, $array[$i]);
        }
    }
}
