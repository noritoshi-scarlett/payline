<?php
declare(strict_types=1);

namespace Payline\App\Infrastructure\Library\Cache\Entity;

use Payline\App\Application\Exception\InvalidArgumentException;
use Payline\App\Infrastructure\Domain\BasicEntityInterface;

/**
 * @template EntityType of BasicEntityInterface
 */
readonly class CacheServiceCursor
{
    /**
     * @param CacheServiceInterface<EntityType> $cacheService
     */
    public function __construct(
        private CacheServiceInterface $cacheService,
        private array $parameters,
        private array $flags
    )
    {
    }

    /**
     * @param callable():array<EntityType> $callable
     * @return array<EntityType>
     * @throws InvalidArgumentException
     */
    public function loadCollection(callable $callable): array
    {
        $cachedData = $this->cacheService->getCachedCollectionByParameters($this->parameters, $this->flags);

        if (false === $cachedData) {
            /** @var array<EntityType> $freshResults */
            $freshResults = $callable();
            return $this->cacheService
                ->saveCollectionInCache($freshResults, $this->parameters, $this->flags, $this->sorter)
                ->getCachedCollectionByParameters($this->parameters, $this->flags);
        }

        return $cachedData;
    }

    /**
     * @param callable():EntityType $callable
     * @return null|EntityType
     * @throws InvalidArgumentException
     */
    public function loadSingle(callable $callable): null|object
    {
        $cachedRecord = $this->cacheService->getCachedRecordByParameters($this->parameters, $this->flags);
        if (isset($cachedRecord)) {
            return $cachedRecord;
        }

        /** @var null|EntityType $freshResult */
        $freshResult = $callable();
        if (isset($freshResult)) {
            return $this->cacheService
                ->saveSingleRecordInCache($freshResult, $this->parameters, $this->flags)
                ->getCachedRecordByParameters($this->parameters, $this->flags);
        }
        return null;
    }
}