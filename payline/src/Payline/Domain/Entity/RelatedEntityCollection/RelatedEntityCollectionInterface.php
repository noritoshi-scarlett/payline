<?php
declare(strict_types=1);

namespace Noritoshi\Payline\Domain\Entity\RelatedEntityCollection;

use Noritoshi\Payline\Interface\Entity\DataHubEntity\DataHubEntityInterface;
use Noritoshi\Payline\Interface\Entity\RelatedEntity\RelatedEntityInterface;

/**
 * @template T of object for DataHubEntityInterface
 * @template V of object for RelatedEntityInterface
 */
interface RelatedEntityCollectionInterface
{
    public function getId(): int;

    /**
     * @return iterable<RelatedEntityInterface<V>>
     */
    public function getRelatedEntities(): iterable;

    /**
     * @param RelatedEntityInterface<V> $entity
     */
    public function hasRelatedEntity(RelatedEntityInterface $entity): bool;

    public function countRelatedEntities(): int;

    /**
     * @return RelatedEntityInterface<V>
     */
    public function getRelatedEntity(int $index): RelatedEntityInterface;

    /**
     * @param callable(RelatedEntityInterface<V>):T $providerForSingleData
     * @param callable(iterable<T>):DataHubEntityInterface<T> $enumeratorForDataCollection
     * @return DataHubEntityInterface<T>
     */
    public function setDataHubByCalculation(
        callable $providerForSingleData, callable $enumeratorForDataCollection
    ): DataHubEntityInterface;

    /**
     * @return DataHubEntityInterface<T>
     */
    public function getCalculatedDataHub(): DataHubEntityInterface;
}
