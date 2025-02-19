<?php
declare(strict_types=1);

namespace Payline\App\Application\Provider;

use Payline\App\Application\Exception\InvalidArgumentException;
use Payline\App\Domain\Cache\CacheServiceInterface;
use Payline\App\Infrastructure\Domain\BasicEntityInterface;

/**
 * @template K of BasicEntityInterface
 */
readonly class CacheServiceCursor
{
    /**
     * @param CacheServiceInterface<K> $cacheService
     */
    public function __construct(
        private CacheServiceInterface $cacheService,
        private array $parameters,
        private array $flags
    )
    {
    }

    /**
     * @param callable():array<K> $callable
     * @return array<K>
     * @throws InvalidArgumentException
     */
    public function loadCollection(callable $callable): array
    {
        $cachedData = $this->cacheService->getCachedCollectionByParameters($this->parameters, $this->flags);

        if (false === $cachedData) {
            /** @var array<K> $freshResults */
            $freshResults = $callable();
            return $this->cacheService
                ->saveCollectionInCache($freshResults, $this->parameters, $this->flags)
                ->getCachedCollectionByParameters($this->parameters, $this->flags);
        }

        return $cachedData;
    }

    /**
     * @param callable():K $callable
     * @return null|K
     * @throws InvalidArgumentException
     */
    public function loadSingle(callable $callable): null|object
    {
        $cachedRecord = $this->cacheService->getCachedRecordByParameters($this->parameters, $this->flags);
        if (isset($cachedRecord)) {
            return $cachedRecord;
        }

        /** @var null|K $freshResult */
        $freshResult = $callable();
        if (isset($freshResult)) {
            return $this->cacheService
                ->saveSingleRecordInCache($freshResult, $this->parameters, $this->flags)
                ->getCachedRecordByParameters($this->parameters, $this->flags);
        }
        return null;
    }
}