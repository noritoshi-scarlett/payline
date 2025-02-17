<?php

namespace Payline\App\Application\Library;

use Payline\App\Application\Exception\InvalidArgumentException;
use Payline\App\Interface\Entity\BasicEntityInterface;
use Payline\App\Interface\Entity\LogEntity\LogEntityInterface;

/**
 * @template K of BasicEntityInterface
 */
class EntitySorter
{
    /**
     * @param iterable<K> &$logs Be converted to array.
     * @throws InvalidArgumentException
     */
    public static function sortByDate(iterable &$logs): void
    {
        $itemsAsArray = is_array($logs) ? $logs : iterator_to_array($logs);
        if (empty($itemsAsArray)) {
            return;
        }

        /**
         * @param K $a
         * @param K $b
         */
        usort($itemsAsArray, function (object $a, object $b): int {
            switch (true) {
                case $a instanceof LogEntityInterface && $b instanceof LogEntityInterface:
                    return $a->getCreatedAt()->getMicrosecond() <=> $b->getCreatedAt()->getMicrosecond();
                case $a instanceof BasicEntityInterface && $b instanceof BasicEntityInterface:
                    return $a->getId() <=> $b->getId();
                default:
                    throw new InvalidArgumentException('Invalid type of entity passed to sorter. Expected BasicEntityInterface.');
            }
        });
        $logs = $itemsAsArray;
    }
}