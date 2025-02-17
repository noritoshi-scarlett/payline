<?php
declare(strict_types=1);

namespace Payline\App\Application\Factory;

use Payline\App\Domain\Entity\RelatedEntityCollection\RelatedEntityCollectionInterface;
use Payline\App\Interface\Entity\LogEntity\LogEntityInterface;
use Payline\App\Interface\Entity\LogEntity\StateEnum\StateEnumInterface;
use Payline\App\Interface\Entity\Source\SourceInterface;

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