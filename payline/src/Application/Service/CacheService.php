<?php
declare(strict_types=1);

namespace Payline\App\Application\Service;

use Payline\App\Application\Exception\InvalidArgumentException;
use Payline\App\Application\Library\Sorter\EntitySorter;
use Payline\App\Application\Provider\CacheServiceCursor;
use Payline\App\Domain\Cache\CacheServiceInterface;
use Payline\App\Infrastructure\Domain\BasicEntityInterface;

/**
 * @template EntityType of BasicEntityInterface
 * @template-implements CacheServiceInterface<EntityType>
 */
class CacheService implements CacheServiceInterface
{
    public const array GET_ALL = ['getAll', '1'];
    public const array GET_NEWEST = ['getNewest', '1'];
    public const array GET_FIRST = ['getFirst', '1'];
    private const array SINGLE_RECORD_FLAGS = [self::GET_NEWEST, self::GET_FIRST];

    /**
     * @var \WeakMap<EntityType, array>
     */
    private \WeakMap $cachingEntitiesDataMap;
    private array $cache;

    public function __construct()
    {
        $this->cache = [];
        $this->cachingEntitiesDataMap = new \WeakMap();
    }

    /**
     * @preturn CacheServiceCursor<EntityType>
     */
    public function getCursor(array $properties, array $flags): CacheServiceCursor
    {
        /** @var CacheServiceCursor<EntityType> return */
        return new CacheServiceCursor($this, $properties, $flags);
    }

    /**
     * @param array<EntityType> $foundByParameters
     * @param array $flags Use some from public const.
     * @throws InvalidArgumentException
     */
    public function saveCollectionInCache(array $foundByParameters, array $parameters, array $flags = []): self
    {
        $cursor = $this->getCursorFromParameters($parameters, $flags);

        /** EntitySorter<EntityType> */
        EntitySorter::sortByDate($foundByParameters);

        $itemsCount = 0;
        foreach ($foundByParameters as $ignored) {
            $itemsCount++;
            if ($this->hasSingleRecordFlag($flags) && $itemsCount > 1) {
                throw new InvalidArgumentException('More than one record found for given parameters');
            }
        }
        foreach ($foundByParameters as $item) {
            $this->cachingEntitiesDataMap[$item] = $this->setStoredData();
        }

        $this->cache[$cursor] = $foundByParameters;
        return $this;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function saveSingleRecordInCache(null|object $singleRecord, array $parameters, array $flags = []): self
    {
        $cursor = $this->getCursorFromParameters($parameters, $flags);

        if (is_null($singleRecord)) {
            unset($this->cache[$cursor]);
            return $this;
        }

        $this->cachingEntitiesDataMap[$singleRecord] = $this->setStoredData();

        $this->cache[$cursor][] = [$singleRecord];
        return $this;
    }

    /**
     * @return array<EntityType>|false Return empty array if cached result is "not found", false if cache not exist.
     * @throws InvalidArgumentException
     */
    public function getCachedCollectionByParameters(array $parameters, array $flags = []): array|false
    {
        $cursor = $this->getCursorFromParameters($parameters, $flags);

        $cachedItems = $this->cache[$cursor] ?? null;
        if (is_null($cachedItems)) {
            unset($this->cache[$cursor]);
            return false;
        }

        return $cachedItems;
    }

    /**
     * @return EntityType|null Return null if not found in cache.
     * @throws InvalidArgumentException
     */
    public function getCachedRecordByParameters(array $parameters, array $flags = []): null|object
    {
        if (!$this->hasSingleRecordFlag($flags)) {
            throw new InvalidArgumentException('Single record flag is required');
        }
        $cursor = $this->getCursorFromParameters($parameters, $flags);

        $cachedItems = $this->cache[$cursor] ?? null;
        if (empty($cachedItems)) {
            unset($this->cache[$cursor]);
            return null;
        }

        return current($this->cache[$cursor]);
    }

    /**
     * @throws InvalidArgumentException
     */
    private function getCursorFromParameters(array $parameters, array $flags = []): string
    {
        ksort($parameters);
        if (!empty($flags)) {
            $parameters = array_merge($parameters, ...($flags));
        }
        if (empty($parameters)) {
            throw new InvalidArgumentException('Empty parameters given');
        }

        return implode('__', array_map(
            fn($name, $value) => $name . '@' . $value,
            array_keys($parameters),
            $parameters
        ));
    }

    private function setStoredData(): array
    {
        return ['storedAt' => time()];
    }

    private function hasSingleRecordFlag(array $flags): bool
    {
        $flattening = fn(array $array): array
            => array_map('current', self::SINGLE_RECORD_FLAGS);

        return !empty(array_intersect(
            $flattening(self::SINGLE_RECORD_FLAGS),
            $flattening($flags)
        ));
    }
}
