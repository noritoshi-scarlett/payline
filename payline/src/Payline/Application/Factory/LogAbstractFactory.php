<?php
declare(strict_types=1);

namespace Noritoshi\Payline\Application\Factory;

use Noritoshi\Payline\Domain\Entity\RelatedEntityCollection\RelatedEntityCollectionInterface;
use Noritoshi\Payline\Interface\Entity\LogEntity\LogEntityInterface;
use Noritoshi\Payline\Interface\Entity\LogEntity\StateEnum\StateEnumInterface;
use Noritoshi\Payline\Interface\Entity\Source\SourceInterface;

/**
 * @template T of object
 * @template V of object
 */
abstract class LogAbstractFactory
{
    /**
     * @param RelatedEntityCollectionInterface<V> $relatedEntityCollection
     * @return LogEntityInterface<T, V>
     */
    abstract public function createLogEntity(
        SourceInterface                  $source,
        RelatedEntityCollectionInterface $relatedEntityCollection,
        StateEnumInterface               $state,
        string                           $message,
        \DateTimeImmutable               $createdAt,
    ): LogEntityInterface;
}