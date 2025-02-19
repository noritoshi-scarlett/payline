<?php

namespace Payline\App\Application\Library\Sorter;

use Payline\App\Application\Exception\InvalidArgumentException;
use Payline\App\Infrastructure\Domain\BasicEntityInterface;
use Payline\App\Infrastructure\Domain\BasicEnumInterface;
use Payline\App\Interface\Entity\LogEntity\LogEntityInterface;

/**
 * @template EntityType of BasicEntityInterface
 */
class EntitySorter
{
    private const SORT_BY_DATE = 'sortByDate';
    private const SORT_BY_ID = 'sortById';

    /**
     * @param array<EntityType> &$collection
     * @throws InvalidArgumentException
     */
    public static function sortByDate(array &$collection, SortDirectionEnum $sortDirection = SortDirectionEnum::DESCENDING): void
    {
        self::sortArrayCollection($collection, __METHOD__, $sortDirection);
    }

    /**
     * @param array<EntityType> &$collection
     * @throws InvalidArgumentException
     */
    public static function sortById(array &$collection, SortDirectionEnum $sortDirection = SortDirectionEnum::ASCENDING): void
    {
        self::sortArrayCollection($collection, __METHOD__, $sortDirection);
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
                        ? $a->getCreatedAt()->getMicrosecond() <=> $b->getCreatedAt()->getMicrosecond()
                        : $b->getCreatedAt()->getMicrosecond() <=> $a->getCreatedAt()->getMicrosecond();
                }
                // fallthrough
            case $a instanceof BasicEntityInterface && $b instanceof BasicEnumInterface:
                return $sortDirection === (SortDirectionEnum::ASCENDING)
                    ? $a->getId() <=> $b->getId()
                    : $b->getId() <=> $a->getId();
            default:
                throw new InvalidArgumentException('Invalid type of entity, expected implementation of BasicEntityInterface');

        }
    }
}
