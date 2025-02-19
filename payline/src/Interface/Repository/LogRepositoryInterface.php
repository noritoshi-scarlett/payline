<?php
declare(strict_types=1);

namespace Payline\App\Interface\Repository;

use Payline\App\Domain\Entity\RelatedEntityCollection\RelatedEntityCollectionInterface;
use Payline\App\Infrastructure\Domain\BasicRepositoryInterface;
use Payline\App\Interface\Entity\LogEntity\LogEntityInterface;
use Payline\App\Interface\Entity\LogEntity\StateEnum\StateEnumInterface;
use Payline\App\Interface\Entity\Source\SourceInterface;

/**
 * @template T of object
 * @template V of object
 */
interface LogRepositoryInterface extends BasicRepositoryInterface
{
    /**
     * @param RelatedEntityCollectionInterface<V> $relatedEntityCollection
     * @return iterable<LogEntityInterface<T, V>>
     */
    public function getAllForRelatedEntityCollection(RelatedEntityCollectionInterface $relatedEntityCollection): iterable;

    /**
     * @param RelatedEntityCollectionInterface<V> $relatedEntityCollection
     * @return LogEntityInterface<T, V>|null
     */
    public function getNewestForRelatedEntityCollection(RelatedEntityCollectionInterface $relatedEntityCollection): ?LogEntityInterface;

    /**
     * @param SourceInterface $source
     * @return iterable<LogEntityInterface<T, V>>
     */
    public function getAllForSource(SourceInterface $source): iterable;

    /**
     * @return iterable<LogEntityInterface<T, V>>
     */
    public function findBySourceAndState(SourceInterface $source, StateEnumInterface $state): iterable;

    /**
     * @param LogEntityInterface<T, V> $log
     * @return bool
     */
    public function save(LogEntityInterface $log): bool;
}