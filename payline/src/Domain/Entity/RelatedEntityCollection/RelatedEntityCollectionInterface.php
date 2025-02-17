<?php
declare(strict_types=1);

namespace Payline\App\Domain\Entity\RelatedEntityCollection;

use Payline\App\Interface\Entity\DataHubEntity\DataHubEntityInterface;
use Payline\App\Interface\Entity\RelatedEntity\RelatedEntityInterface;

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
     * @return DataHubEntityInterface<V>
     */
    public function calculateDataHub(
        callable $providerForSingleData, callable $enumeratorForDataCollection
    ): DataHubEntityInterface;

    /**
     * @return DataHubEntityInterface<T>
     */
    public function getDataHub(): DataHubEntityInterface;
}