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
            $cachedData = $this->cacheService
                ->saveCollectionInCache($freshResults, $this->parameters, $this->flags)
                ->getCachedCollectionByParameters($this->parameters, $this->flags);
            if (!is_array($cachedData)) {
                //TODO Exception throw about infrastructure error
            }
            return $cachedData;
        }

        return $cachedData;
    }

    /**
     * @param callable():EntityType $callable Allow to return null.
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
            $cachedRecord = $this->cacheService
                ->saveSingleRecordInCache($freshResult, $this->parameters, $this->flags)
                ->getCachedRecordByParameters($this->parameters, $this->flags);
            if (!isset($cachedRecord)) {
                //TODO Exception throw about infrastructure error
            }
            return $cachedRecord;
        }
        return null;
    }
}