<?php
declare(strict_types=1);

namespace Payline\App\Application\Manager;

use Payline\App\Application\Exception\InvalidArgumentException;
use Payline\App\Application\Library\Normalizer\CollectionNormalizer;
use Payline\App\Application\Provider\CacheServiceCursor;
use Payline\App\Application\Service\CacheService;
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
    }

    /**
     * @return array<LogEntityInterface<T, V>>
     * @throws InvalidArgumentException
     */
    public function getLogsForSourceAndState(SourceInterface $source, StateEnumInterface $state): array
    {
        $parameters = [$source::class => $source->getId(), $state::class => $state->name];

        /** @var CacheServiceCursor<LogEntityInterface<T, V>> $cursor */
        $cursor = $this->logCacheService->getCursor($parameters, [CacheService::GET_ALL]);
        return $cursor->loadCollection(
            /**
             * @return array<LogEntityInterface<T, V>>
             */
            fn():array => CollectionNormalizer::toArray($this->logRepository->findBySourceAndState($source, $state)));
    }
}
