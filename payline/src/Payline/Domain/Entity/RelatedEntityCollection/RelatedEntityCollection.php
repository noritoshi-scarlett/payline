<?php
declare(strict_types=1);

namespace Noritoshi\Payline\Domain\Entity\RelatedEntityCollection;

use Noritoshi\Payline\Interface\Entity\DataHubEntity\DataHubEntityInterface;
use Noritoshi\Payline\Interface\Entity\RelatedEntity\RelatedEntityInterface;

/**
 * @template T of object for DataHubEntityInterface
 * @template V of object for RelatedEntityInterface
 * @template-implements RelatedEntityCollectionInterface<T, V>
 */
readonly class RelatedEntityCollection implements RelatedEntityCollectionInterface
{
    /** @var DataHubEntityInterface<T> $dataHub  */
    private DataHubEntityInterface $dataHub;

    /**
     * @param array<RelatedEntityInterface<V>> $relatedEntities
     */
    public function __construct(
        private int   $id,
        private array $relatedEntities
    )
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return iterable<RelatedEntityInterface<V>>
     */
    public function getRelatedEntities(): iterable
    {
        return $this->relatedEntities;
    }

    /**
     * @param RelatedEntityInterface<V> $entity
     */
    public function hasRelatedEntity(RelatedEntityInterface $entity): bool
    {
        return array_any(
            $this->relatedEntities,
            /**
             * @param RelatedEntityInterface<V> $relatedEntity
             */
            fn(RelatedEntityInterface $relatedEntity) => $relatedEntity->getId() === $entity->getId()
        );
    }

    public function countRelatedEntities(): int
    {
        return count($this->relatedEntities);
    }

    /**
     * @return RelatedEntityInterface<V>
     */
    public function getRelatedEntity(int $index): RelatedEntityInterface
    {
        /** @var RelatedEntityInterface<V> $item */
        $item = $this->relatedEntities[$index];
        return $item;
    }

    /**
     * @return DataHubEntityInterface<T>
     */
    public function getCalculatedDataHub(): DataHubEntityInterface
    {
        return $this->dataHub;
    }

    /**
     * @param callable(RelatedEntityInterface<V>):T $providerForSingleData
     * @param callable(iterable<T>):DataHubEntityInterface<T> $enumeratorForDataCollection
     * @return DataHubEntityInterface<T>
     */
    public function setDataHubByCalculation(
        callable $providerForSingleData,
        callable $enumeratorForDataCollection
    ): DataHubEntityInterface
    {
        $data = [];
        foreach ($this->relatedEntities as $relatedEntity) {
            $data[] = $providerForSingleData($relatedEntity);
        }

        $this->dataHub = $enumeratorForDataCollection($data);
        return $this->dataHub;
    }
}
