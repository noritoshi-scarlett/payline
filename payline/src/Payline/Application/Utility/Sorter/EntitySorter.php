<?php

namespace Noritoshi\Payline\Application\Utility\Sorter;

use Noritoshi\Payline\Application\Exception\InvalidArgumentException;
use Noritoshi\Payline\Infrastructure\Domain\BasicEntityInterface;
use Noritoshi\Payline\Interface\Entity\LogEntity\LogEntityInterface;

/**
 * @template EntityType of BasicEntityInterface
 */
class EntitySorter
{
    private const string SORT_BY_DATE = 'sortByDate';
    private const string SORT_BY_ID = 'sortById';

    /**
     * @param array<EntityType> $collection
     * @return array<EntityType>
     * @throws InvalidArgumentException
     */
    public static function sortByDate(array $collection, SortDirectionEnum $sortDirection): array
    {
        self::sortArrayCollection($collection, __FUNCTION__, $sortDirection);
        return $collection;
    }

    /**
     * @param array<EntityType> $collection
     * @return array<EntityType>
     * @throws InvalidArgumentException
     */
    public static function sortById(array $collection, SortDirectionEnum $sortDirection): array
    {
        self::sortArrayCollection($collection, __FUNCTION__, $sortDirection);
        return $collection;
    }

    /**
     * @param array<EntityType> &$collection
     * @throws InvalidArgumentException
     */
    private static function sortArrayCollection(array &$collection, string $methodSortName, SortDirectionEnum $sortDirection): void
    {
        if (empty($collection)) {
            return;
        }

        usort($collection, fn($a, $b):int => self::compareItems($a, $b, $methodSortName, $sortDirection));
    }

    /**
     * @param EntityType $a
     * @param EntityType $b
     * @throws InvalidArgumentException
     */
    private static function compareItems(object $a, object $b, string $methodSortName, SortDirectionEnum $sortDirection): int {
        switch (true) {
            case $a instanceof LogEntityInterface && $b instanceof LogEntityInterface:
                if ($methodSortName === self::SORT_BY_DATE) {
                    return $sortDirection === (SortDirectionEnum::ASCENDING)
                        ? $a->getCreatedAt()->getTimestamp() <=> $b->getCreatedAt()->getTimestamp()
                        : $b->getCreatedAt()->getTimestamp() <=> $a->getCreatedAt()->getTimestamp();
                }
                // fallthrough
            case $a instanceof BasicEntityInterface && $b instanceof BasicEntityInterface:
                return $sortDirection === (SortDirectionEnum::ASCENDING)
                    ? $a->getId() <=> $b->getId()
                    : $b->getId() <=> $a->getId();
            default:
                throw new InvalidArgumentException('Invalid type of entity, expected implementation of BasicEntityInterface');

        }
    }
}
