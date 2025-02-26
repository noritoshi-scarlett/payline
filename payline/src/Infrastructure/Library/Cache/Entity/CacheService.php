<?php
declare(strict_types=1);

namespace Payline\App\Infrastructure\Library\Cache\Entity;

use Payline\App\Application\Exception\InvalidArgumentException;
use Payline\App\Infrastructure\Domain\BasicEntityInterface;
use Payline\App\Infrastructure\Library\Cache\CacheSystemInterface;
use WeakMap;

/**
 * @template EntityType of BasicEntityInterface
 * @template-implements CacheServiceInterface<EntityType>
 */
class CacheService implements CacheServiceInterface
{
    /**
     * This flag auto-remove old items from store.
     */
    public const string FETCH_NOT_OLDER_THAN_IN_SECONDS_FLAG_NAME = 'fetchNotOlderThan';
    public const array SINGLE_NEWEST_FLAG = ['NewestRecord', '1'];
    public const array SINGLE_FIRST_FLAG = ['FirstRecord', '1'];
    public const array SINGLE_UNIQUE_FLAG = ['UniqueRecord', '1'];
    public const array STORE_FIRST_RECORD_FLAG = ['storeFirstRecord', '1'];
    public const array ALL_FLAG = ['AllRecords', '1'];
    private const array SINGLE_RECORD_FLAGS = [self::SINGLE_NEWEST_FLAG, self::SINGLE_FIRST_FLAG, self::SINGLE_UNIQUE_FLAG];

    /**
     * @var WeakMap<EntityType, array>
     */
    private WeakMap $cachingEntitiesDataMap;
    private array $cache;

    public bool $autosave {
        get {
            return $this->autosave;
        }
        set {
            $this->autosave = $value;
        }
    }

    /**
     * @param class-string<EntityType> $cacheNamespace
     */
    public function __construct(
        private readonly CacheSystemInterface $cacheSystem,
        private readonly string $cacheNamespace,
    )
    {
        $this->cache = [];
        $this->cachingEntitiesDataMap = new WeakMap();
        $this->cacheSystem->prefixIdentification = $this->cacheNamespace;

        // Load from cache system
        $keys = $this->cacheSystem->loadAllKeys();
        foreach ($keys as $key) {
            $data = $this->loadFromCacheSystem($key);
            if ($data !== null) {
                $this->cache[$key] = $data;
            }
        }
    }

    public function __destruct()
    {
        foreach ($this->cache as $key => $data) {
            $this->storeInCacheSystem($key, $data);
        }
        $this->cacheSystem->storeAllKeys(array_keys($this->cache));
        $this->cache = [];
    }

    private function loadFromCacheSystem(string $key): ?array
    {
        return $this->cacheSystem->getByKey($key);
    }

    private function storeInCacheSystem(string $key, array $data): void
    {
        $this->cacheSystem->saveByKey($key, $data);
    }

    /**
     * @return CacheServiceCursor<EntityType>
     */
    public function getCursor(array $properties, array $flags): CacheServiceCursor
    {
        /** @var CacheServiceCursor<EntityType> return */
        return new CacheServiceCursor($this, $properties, $flags);
    }

    /**
     * @param array<EntityType> $collection
     * @param array $flags Optional. Use some from public const.
     * @throws InvalidArgumentException
     */
    public function saveCollectionInCache(array $collection, array $parameters, array $flags = []): self
    {
        $key = $this->getKeyFromParameters($parameters, $flags);

        $itemsCount = 0;
        foreach ($collection as $ignored) {
            $itemsCount++;
            if ($itemsCount > 1) {
                if ($this->hasSingleRecordFlag($flags)) {
                    throw new InvalidArgumentException('More than one record found for given flags');
                }
                if ($this->hasStoreFirstRecordFlag($flags)) {
                    $collection = array_slice($collection, 0, 1);
                    break;
                }
            }
        }
        foreach ($collection as $item) {
            $this->cachingEntitiesDataMap[$item] = $this->setStoredData();
        }

        $this->cache[$key] = $collection;
        if ($this->autosave) {
            $this->storeInCacheSystem($key, $this->cache[$key]);
        }
        return $this;
    }

    /**
     * @param EntityType|null $singleRecord
     * @param array $flags Use some from public const as array.
     * @throws InvalidArgumentException
     */
    public function saveSingleRecordInCache(null|object $singleRecord, array $parameters, array $flags = [self::SINGLE_UNIQUE_FLAG]): self
    {
        if (!$this->hasSingleRecordFlag($flags)) {
            throw new InvalidArgumentException('Single record flag is required');
        }

        $key = $this->getKeyFromParameters($parameters, $flags);

        if (is_null($singleRecord)) {
            unset($this->cache[$key]);
            return $this;
        }

        $this->cachingEntitiesDataMap[$singleRecord] = $this->setStoredData();

        $this->cache[$key] = [$singleRecord];
        if ($this->autosave) {
            $this->storeInCacheSystem($key, $this->cache[$key]);
        }
        return $this;
    }

    /**
     * @param array $flags Optional. Use some from public const as array.
     * @return EntityType|false Return empty array if cached result is "not found", false if cache not exist.
     * @throws InvalidArgumentException
     */
    public function getCachedCollectionByParameters(array $parameters, array $flags = []): array|false
    {
        $key = $this->getKeyFromParameters($parameters, $flags);

        $cachedCollection = $this->cache[$key] ?? ($this->autosave ? $this->loadFromCacheSystem($key) : null);
        if (is_null($cachedCollection)) {
            unset($this->cache[$key]);
            return false;
        }
        foreach ($cachedCollection as $item) {
            if (isset($this->cachingEntitiesDataMap[$item])) {
                $oldLimit = $this->getOldLimitInSeconds($flags);
                if ($oldLimit !== false && (time() - $this->cachingEntitiesDataMap[$item]['storedAt']) > $oldLimit) {
                    unset($this->cache[$key]);
                    return false;
                }
            }
        }

        return $cachedCollection;
    }

    /**
     * @param array $flags Use some from public const as array.
     * @return EntityType|null Return null if not found in cache.
     * @throws InvalidArgumentException
     */
    public function getCachedRecordByParameters(array $parameters, array $flags = [self::SINGLE_UNIQUE_FLAG]): null|object
    {
        if (!$this->hasSingleRecordFlag($flags)) {
            throw new InvalidArgumentException('Single record flag is required');
        }
        $key = $this->getKeyFromParameters($parameters, $flags);

        $cachedCollection = $this->cache[$key] ?? ($this->autosave ? $this->loadFromCacheSystem($key) : null);
        if (empty($cachedCollection)) {
            unset($this->cache[$key]);
            return null;
        }
        $cachedRecord = current($this->cache[$key]);
        $oldLimit = $this->getOldLimitInSeconds($flags);
        if ($oldLimit !== false && (time() - $this->cachingEntitiesDataMap[$cachedRecord]['storedAt']) > $oldLimit) {
            unset($this->cache[$key]);
            return null;
        }

        return $cachedRecord;
    }

    /**
     * @throws InvalidArgumentException
     */
    private function getKeyFromParameters(array $parameters, array $flags): string
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
        return !empty(array_intersect(
            $this->flattening(self::SINGLE_RECORD_FLAGS),
            $this->flattening($flags)
        ));
    }
    
    private function getOldLimitInSeconds(array $flags): int|false
    {
        $olderThan = $flags[self::FETCH_NOT_OLDER_THAN_IN_SECONDS_FLAG_NAME] ?? false;
        return isset($olderThan) && (int)$olderThan > 0
            ? (int)$olderThan
            : false;
    }
    
    private function hasStoreFirstRecordFlag(array $flags): bool
    {
        return !empty(array_intersect(
            self::STORE_FIRST_RECORD_FLAG,
            $this->flattening($flags)
        ));
    }
    
    private function flattening(array $array): array
    {
       return array_map('current', $array);
    }
}
