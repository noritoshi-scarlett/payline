<?php
declare(strict_types=1);

namespace Noritoshi\Payline\Application\Manager;

use Noritoshi\Payline\Application\Exception\InvalidArgumentException;
use Noritoshi\Payline\Application\Factory\CacheServiceFactory;
use Noritoshi\Payline\Application\Utility\Normalizer\CollectionNormalizer;
use Noritoshi\Payline\Application\Utility\Sorter\EntitySorter;
use Noritoshi\Payline\Application\Utility\Sorter\SortDirectionEnum;
use Noritoshi\Payline\Infrastructure\Library\Cache\Entity\CacheService;
use Noritoshi\Payline\Infrastructure\Library\Cache\Entity\CacheServiceCursor;
use Noritoshi\Payline\Interface\Entity\LogEntity\LogEntityInterface;
use Noritoshi\Payline\Interface\Entity\LogEntity\StateEnum\StateEnumInterface;
use Noritoshi\Payline\Interface\Entity\Source\SourceInterface;
use Noritoshi\Payline\Interface\Repository\LogRepositoryInterface;

/**
 * @template T of object for LogEntityInterface
 * @template V of object for LogEntityInterface
 */
readonly class SourceLogsManager
{
    /**
     * @template EntityType of LogEntityInterface<T, V>
     * @param LogRepositoryInterface<T, V> $logRepository
     * @param class-string<EntityType> $logClass
     */
    public function __construct(
        private LogRepositoryInterface $logRepository,
        string                         $logClass,
        CacheServiceFactory            $cacheServiceFactory,
    ) {
        /** @var CacheService<EntityType> $logCacheService */
        $logCacheService = $cacheServiceFactory->create($logClass, true);

        $this->logCacheService = $logCacheService;
    }

    /**
     * @template EntityType of LogEntityInterface<T, V>
     * @var CacheService<EntityType> $logCacheService
     */
    private CacheService $logCacheService;

    /**
     * @return array<LogEntityInterface<T, V>>
     * @throws InvalidArgumentException
     */
    public function getLogsForSourceAndState(SourceInterface $source, StateEnumInterface $state): array
    {
        $parameters = [$source::class => $source->getId(), $state::class => $state->name];

        /** @var CacheServiceCursor<LogEntityInterface<T, V>> $cursor */
        $cursor = $this->logCacheService->getCursor($parameters, [CacheService::ALL_FLAG]);
        return $cursor->loadCollection(
        /**
         * @return array<LogEntityInterface<T, V>>
         * @throws InvalidArgumentException
         */
            fn():array => EntitySorter::sortById(
                CollectionNormalizer::toArray($this->logRepository->findBySourceAndState($source, $state)),
                SortDirectionEnum::ASCENDING
            )
        );
    }
}
