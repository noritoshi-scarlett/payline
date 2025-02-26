<?php
declare(strict_types=1);

namespace Payline\App\Application\Manager;

use Payline\App\Application\Exception\InvalidArgumentException;
use Payline\App\Application\Utility\Normalizer\CollectionNormalizer;
use Payline\App\Application\Utility\Sorter\EntitySorter;
use Payline\App\Application\Utility\Sorter\SortDirectionEnum;
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
readonly class SourceLogsManager
{
    /**
     * @param CacheService<LogEntityInterface<T, V>> $logCacheService
     * @param LogRepositoryInterface<T, V> $logRepository
     */
    public function __construct(
        private CacheService           $logCacheService,
        private LogRepositoryInterface $logRepository,
    )
    {
        $this->logCacheService->autosave = true;
    }

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
