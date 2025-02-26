<?php
declare(strict_types=1);

namespace Payline\App\Application\Manager;

use Payline\App\Application\Exception\InvalidArgumentException;
use Payline\App\Application\Exception\Validation\InvalidDateException;
use Payline\App\Application\Exception\Validation\InvalidLogStateEnumException;
use Payline\App\Application\Factory\LogAbstractFactory;
use Payline\App\Application\Utility\Normalizer\CollectionNormalizer;
use Payline\App\Application\Utility\Sorter\EntitySorter;
use Payline\App\Application\Utility\Sorter\SortDirectionEnum;
use Payline\App\Application\Utility\Validator\LogValidator;
use Payline\App\Domain\Entity\RelatedEntityCollection\RelatedEntityCollectionInterface;
use Payline\App\Infrastructure\Library\Cache\Entity\CacheService;
use Payline\App\Infrastructure\Library\Cache\Entity\CacheServiceCursor;
use Payline\App\Interface\Entity\LogEntity\LogEntityInterface;
use Payline\App\Interface\Entity\LogEntity\StateEnum\StateEnumInterface;
use Payline\App\Interface\Entity\Source\SourceInterface;
use Payline\App\Interface\Repository\LogRepositoryInterface;

/**
 * @template T of object for LogEntityInterface
 * @template V of object for LogEntityInterface
 */
readonly class RelatedEntityCollectionLogsManager
{

    /**
     * @param CacheService<T, V> $logCacheService
     * @param LogAbstractFactory<T, V> $logFactory
     */
    public function __construct(
        private CacheService           $logCacheService,
        private LogRepositoryInterface $logRepository,
        private LogAbstractFactory     $logFactory,
    ) {
        $this->logCacheService->autosave = true;
    }

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
        LogValidator::newLogDataCompareToNewestLog(
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