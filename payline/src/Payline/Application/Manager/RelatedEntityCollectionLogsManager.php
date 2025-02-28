<?php
declare(strict_types=1);

namespace Noritoshi\Payline\Application\Manager;

use Noritoshi\Payline\Application\Exception\InvalidArgumentException;
use Noritoshi\Payline\Application\Exception\Validation\InvalidDateException;
use Noritoshi\Payline\Application\Exception\Validation\InvalidLogStateEnumException;
use Noritoshi\Payline\Application\Factory\CacheServiceFactory;
use Noritoshi\Payline\Application\Factory\LogAbstractFactory;
use Noritoshi\Payline\Application\Utility\Normalizer\CollectionNormalizer;
use Noritoshi\Payline\Application\Utility\Sorter\EntitySorter;
use Noritoshi\Payline\Application\Utility\Sorter\SortDirectionEnum;
use Noritoshi\Payline\Application\Utility\Validator\LogValidator;
use Noritoshi\Payline\Domain\Entity\RelatedEntityCollection\RelatedEntityCollectionInterface;
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
readonly class RelatedEntityCollectionLogsManager
{
    /**
     * @template EntityType of LogEntityInterface<T, V>
     * @param LogAbstractFactory<T, V> $logFactory
     * @param class-string<EntityType> $logClass
     */
    public function __construct(
        private LogRepositoryInterface $logRepository,
        private LogAbstractFactory     $logFactory,
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
     * @param RelatedEntityCollectionInterface<V> $relatedEntityCollection
     * @return LogEntityInterface<T, V>
     * @throws InvalidLogStateEnumException
     * @throws InvalidArgumentException
     * @throws InvalidDateException
     */
    public function createLog(
        SourceInterface                  $source,
        RelatedEntityCollectionInterface $relatedEntityCollection,
        StateEnumInterface               $state,
        string                           $message,
        \DateTimeImmutable               $createdAt,
    ): LogEntityInterface
    {
        LogValidator::newLogDataCompareToLatestLog(
                $this->getNewestLog($relatedEntityCollection),
                $source,
                $state,
                $createdAt
        );

        /** @var LogEntityInterface<T, V> $log */
        $log = $this->logFactory->createLogEntity(
            $source,
            $relatedEntityCollection,
            $state,
            $message,
            $createdAt,
        );

        if ($this->logRepository->save($log)) {
            return $log;
        }
        throw new InvalidArgumentException('Log could not be saved, because repository have problems. Check logs for more information');
    }

    /**
     * @return null|LogEntityInterface<T, V>
     * @throws InvalidArgumentException
     */
    public function getNewestLog(RelatedEntityCollectionInterface $relatedEntityCollection): ?LogEntityInterface
    {
        $parameters = [$relatedEntityCollection::class => $relatedEntityCollection->getId()];

        /** @var CacheServiceCursor<LogEntityInterface<T, V>> $cursor */
        $cursor = $this->logCacheService->getCursor($parameters, [CacheService::SINGLE_NEWEST_FLAG]);
        return $cursor->loadSingle(
            /**
             * @return LogEntityInterface<T, V>|null
             */
            fn(): ?LogEntityInterface => $this->logRepository->getNewestForRelatedEntityCollection($relatedEntityCollection)
        );
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getAllLogs(RelatedEntityCollectionInterface $relatedEntityCollection): array
    {
        $parameters = [$relatedEntityCollection::class => $relatedEntityCollection->getId()];

        /** @var CacheServiceCursor<LogEntityInterface<T, V>> $cursor */
        $cursor = $this->logCacheService->getCursor($parameters, [CacheService::ALL_FLAG]);
        return $cursor->loadCollection(
        /**
         * @return array<LogEntityInterface<T, V>>
         * @throws InvalidArgumentException
         */
            fn():array => EntitySorter::sortByDate(
                CollectionNormalizer::toArray($this->logRepository->getAllForRelatedEntityCollection($relatedEntityCollection)),
                SortDirectionEnum::DESCENDING
            )
        );
    }
}