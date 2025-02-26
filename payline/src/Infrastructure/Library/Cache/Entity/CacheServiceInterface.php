<?php
declare(strict_types=1);

namespace Payline\App\Infrastructure\Library\Cache\Entity;

use Payline\App\Application\Exception\InvalidArgumentException;
use Payline\App\Infrastructure\Domain\BasicEntityInterface;

/**
 * @template EntityType of BasicEntityInterface
 */
interface CacheServiceInterface
{
    public bool $autosave {
        get;
        set;
    }

    /**
     * @return CacheServiceCursor<EntityType>
     */
    public function getCursor(array $properties, array $flags): CacheServiceCursor;

    /**
     * @param array<EntityType> $collection
     * @param array $flags Use some from public const.
     * @throws InvalidArgumentException
     */
    public function saveCollectionInCache(array $collection, array $parameters, array $flags = []): self;

    /**
     * @param EntityType|null $singleRecord
     * @param array $flags Use some from public const.
     * @throws InvalidArgumentException
     */
    public function saveSingleRecordInCache(?object $singleRecord, array $parameters, array $flags): self;

    /**
     * @return array<EntityType>|false Return empty array if cached result is "not found", false if cache not exist.
     * @throws InvalidArgumentException
     */
    public function getCachedCollectionByParameters(array $parameters, array $flags = []): array|false;

    /**
     * @return EntityType|null Return null if not found in cache.
     * @throws InvalidArgumentException
     */
    public function getCachedRecordByParameters(array $parameters, array $flags): ?object;
}
