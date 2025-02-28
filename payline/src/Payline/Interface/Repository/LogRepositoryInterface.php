<?php
declare(strict_types=1);

namespace Noritoshi\Payline\Interface\Repository;

use Noritoshi\Payline\Domain\Entity\RelatedEntityCollection\RelatedEntityCollectionInterface;
use Noritoshi\Payline\Infrastructure\Domain\BasicRepositoryInterface;
use Noritoshi\Payline\Interface\Entity\LogEntity\LogEntityInterface;
use Noritoshi\Payline\Interface\Entity\LogEntity\StateEnum\StateEnumInterface;
use Noritoshi\Payline\Interface\Entity\Source\SourceInterface;

/**
 * @template T of object
 * @template V of object
 */
interface LogRepositoryInterface extends BasicRepositoryInterface
{
    /**
     * @param RelatedEntityCollectionInterface<V> $relatedEntityCollection
     * @return iterable<LogEntityInterface<V, T>>
     */
    public function getAllForRelatedEntityCollection(RelatedEntityCollectionInterface $relatedEntityCollection): iterable;

    /**
     * @param RelatedEntityCollectionInterface<V> $relatedEntityCollection
     * @return LogEntityInterface<T, V>|null
     */
    public function getNewestForRelatedEntityCollection(RelatedEntityCollectionInterface $relatedEntityCollection): ?LogEntityInterface;

    /**
     * @param SourceInterface $source
     * @return iterable<LogEntityInterface<V, T>>
     */
    public function getAllForSource(SourceInterface $source): iterable;

    /**
     * @return iterable<LogEntityInterface<V, T>>
     */
    public function findBySourceAndState(SourceInterface $source, StateEnumInterface $state): iterable;

    /**
     * @param LogEntityInterface<T, V> $log
     * @return bool
     */
    public function save(LogEntityInterface $log): bool;
}