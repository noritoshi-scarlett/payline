<?php
declare(strict_types=1);

namespace Payline\App\Domain\Model;

use Payline\App\Interface\Entity\DataHubEntity\DataHubEntityInterface;
use Payline\App\Domain\Entity\RelatedEntityCollection\RelatedEntityCollectionInterface;
use Payline\App\Interface\Entity\RelatedEntity\RelatedEntityInterface;

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
        foreach ($this->relatedEntities as $relatedEntity) {
            if ($relatedEntity->getId() === $entity->getId()) {
                return true;
            }
        }
        return false;
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
    public function getDataHub(): DataHubEntityInterface
    {
        return $this->dataHub;
    }

    /**
     * @param callable(RelatedEntityInterface<V>):T $providerForSingleData
     * @param callable(iterable<T>):DataHubEntityInterface<T> $enumeratorForDataCollection
     * @return DataHubEntityInterface<V>
     */
    public function calculateDataHub(
        callable $providerForSingleData, callable $enumeratorForDataCollection
    ): DataHubEntityInterface
    {
        $data = [];
        foreach ($this->relatedEntities as $relatedEntity) {
            $data[] = $providerForSingleData($relatedEntity);
        }
        return $enumeratorForDataCollection($data);
    }
}