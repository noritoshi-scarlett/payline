<?php
declare(strict_types=1);

namespace Noritoshi\Payline\Test\Infrastructure\Library\Cache\Entity;

use Noritoshi\Payline\Application\Exception\InvalidArgumentException;
use Noritoshi\Payline\Infrastructure\Domain\BasicEntityInterface;
use Noritoshi\Payline\Infrastructure\Library\Cache\Entity\CacheService;
use Noritoshi\Payline\Infrastructure\Library\Cache\Entity\CacheServiceCursor;
use Noritoshi\Payline\Infrastructure\Library\Cache\Entity\CacheServiceInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CacheServiceCursorTest extends TestCase
{
    private CacheServiceInterface&MockObject $cacheServiceMock;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->cacheServiceMock = $this->createMock(CacheServiceInterface::class);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testLoadCollectionReturnsDataFromCacheWhenAvailable(): void
    {
        $parameters = ['user_id' => 123];
        $flags = [CacheService::ALL_FLAG];
        $cachedData = [
            new class implements BasicEntityInterface {public function getId(): int { return 3;}},
            new class implements BasicEntityInterface {public function getId(): int { return 5;}}
        ];

        $this->cacheServiceMock->method('getCachedCollectionByParameters')->willReturn($cachedData);
        $cursor = new CacheServiceCursor($this->cacheServiceMock, $parameters, $flags);
        $result = $cursor->loadCollection(
            fn():array => $this->fail("Callable should not be executed when data is in cache")
        );

        $this->assertSame($cachedData, $result, "Data should come from cache when available");
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testLoadCollectionExecutesCallableAndCachesResultIfNotInCache(): void
    {
        $parameters = ['user_id' => 123];
        $flags = [CacheService::ALL_FLAG];
        $freshData = [
            new class implements BasicEntityInterface {public function getId(): int { return 4;}},
            new class implements BasicEntityInterface {public function getId(): int { return 6;}}
        ];

        $invokedCount = $this->exactly(2);
        $this->cacheServiceMock
            ->expects($invokedCount)
            ->method('getCachedCollectionByParameters')
            ->willReturnCallback(function($parameters) use($freshData, &$invokedCount) {
                return match ($invokedCount->numberOfInvocations()) {
                    1 => false,
                    2 => $freshData,
                };
            });
        $this->cacheServiceMock
            ->expects($this->once())
            ->method('saveCollectionInCache')
            ->with($freshData, $parameters, $flags)
            ->willReturn($this->cacheServiceMock);

        $cursor = new CacheServiceCursor($this->cacheServiceMock, $parameters, $flags);
        $result = $cursor->loadCollection(fn():array => $freshData);

        $this->assertSame($freshData, $result, "Data should match fresh data loaded from callable when cache is empty");
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testLoadSingleReturnsDataFromCacheWhenAvailable(): void
    {
        $parameters = ['record_id' => 1];
        $flags = [CacheService::SINGLE_UNIQUE_FLAG];
        $cachedRecord = new class implements BasicEntityInterface {public function getId(): int { return 10;}};

        $this->cacheServiceMock->method('getCachedRecordByParameters')->willReturn($cachedRecord);
        $cursor = new CacheServiceCursor($this->cacheServiceMock, $parameters, $flags);
        $result = $cursor->loadSingle(
            fn():BasicEntityInterface => $this->fail("Callable should not be executed when data is in cache")
        );

        $this->assertSame($cachedRecord, $result, "Record should come from cache when available");
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testLoadSingleExecutesCallableAndCachesResultIfNotInCache(): void
    {
        $parameters = ['record_id' => 1];
        $flags = [CacheService::SINGLE_UNIQUE_FLAG];
        $freshRecord = new class implements BasicEntityInterface {public function getId(): int { return 20;}};

        $invokedCount = $this->exactly(2);
        $this->cacheServiceMock
            ->expects($invokedCount)
            ->method('getCachedRecordByParameters')
            ->willReturnCallback(function($parameters) use($freshRecord, &$invokedCount) {
                return match ($invokedCount->numberOfInvocations()) {
                    1 => null,
                    2 => $freshRecord,
                };
            });
        $this->cacheServiceMock
            ->expects($this->once())
            ->method('saveSingleRecordInCache')
            ->with($freshRecord, $parameters, $flags)
            ->willReturn($this->cacheServiceMock);

        $cursor = new CacheServiceCursor($this->cacheServiceMock, $parameters, $flags);
        $result = $cursor->loadSingle(fn():BasicEntityInterface => $freshRecord);

        $this->assertSame($freshRecord, $result, "Record should match fresh record loaded from callable when cache is empty");
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testLoadSingleReturnsNullIfCallableReturnsNullAndNotInCache(): void
    {
        $parameters = ['record_id' => 1];
        $flags = [CacheService::SINGLE_UNIQUE_FLAG];

        $this->cacheServiceMock
            ->method('getCachedRecordByParameters')
            ->willReturn(null);

        $cursor = new CacheServiceCursor($this->cacheServiceMock, $parameters, $flags);
        $result = $cursor->loadSingle(fn():?BasicEntityInterface => null);

        $this->assertNull($result, "Result should be null when both cache and callable return null");
    }
}
