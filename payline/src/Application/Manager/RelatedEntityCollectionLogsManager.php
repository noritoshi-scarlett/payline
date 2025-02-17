<?php
declare(strict_types=1);

namespace Payline\App\Application\Manager;

use Payline\App\Application\Exception\InvalidArgumentException;
use Payline\App\Application\Exception\InvalidLogStateEnumException;
use Payline\App\Application\Factory\LogAbstractFactory;
use Payline\App\Application\Provider\CacheServiceCursor;
use Payline\App\Application\Service\CacheService;
use Payline\App\Domain\Entity\RelatedEntityCollection\RelatedEntityCollectionInterface;
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
    }

    /**
     * @param RelatedEntityCollectionInterface<V> $relatedEntityCollection
     * @return LogEntityInterface<T, V>
     * @throws InvalidLogStateEnumException
     * @throws InvalidArgumentException
     */
    public function createLog(
        SourceInterface                  $source,
        RelatedEntityCollectionInterface $relatedEntityCollection,
        StateEnumInterface               $state,
        string                           $message,
        \DateTimeImmutable               $createdAt,
    ): LogEntityInterface
    {
        if (!$this->isStateAllowedForNextLog($source, $relatedEntityCollection, $state)) {
            throw new InvalidLogStateEnumException(sprintf('State [%s] is not allowed for next log', $state::class));
        }

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
        //TODO throw repositoruy exception
    }

    /**
     * @throws InvalidArgumentException
     */
    public function isStateAllowedForNextLog(
        SourceInterface $source,
        RelatedEntityCollectionInterface $relatedEntityCollection,
        StateEnumInterface $state
    ): bool
    {
        return $source->isStateAllowedForNextLog(
            $this->getNewestLog($relatedEntityCollection),
            $state
        );
    }

    /**
     * @return null|LogEntityInterface<T, V>
     * @throws InvalidArgumentException
     */
    public function getNewestLog(RelatedEntityCollectionInterface $relatedEntityCollection): ?LogEntityInterface
    {
        $parameters = [$relatedEntityCollection::class => $relatedEntityCollection->getId()];

        /** @var CacheServiceCursor<LogEntityInterface<T, V>> $cursor */
        $cursor = $this->logCacheService->getCursor($parameters, [CacheService::GET_NEWEST]);
        return $cursor->loadSingle(
            /**
             * @@return LogEntityInterface<T, V>|null
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
        $cursor = $this->logCacheService->getCursor($parameters, [CacheService::GET_ALL]);
        return $cursor->loadCollection(
            /**
             * @@return iterable<LogEntityInterface<T, V>>
             */
            fn(): iterable => $this->logRepository->getAllForRelatedEntityCollection($relatedEntityCollection)
        );
    }
}