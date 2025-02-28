<?php
declare(strict_types=1);

namespace Noritoshi\Payline\Test\Infrastructure\Library\Cache\Entity;

use Noritoshi\Payline\Infrastructure\Library\Cache\Entity\CacheService;
use Noritoshi\Payline\Test\TestDataProviders\ReflectionUtility;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Noritoshi\Payline\Application\Exception\InvalidArgumentException;
use Noritoshi\Payline\Infrastructure\Domain\BasicEntityInterface;
use Noritoshi\Payline\Infrastructure\Library\Cache\CacheSystemInterface;
use Noritoshi\Payline\Infrastructure\Library\Cache\Entity\CacheServiceCursor;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionException;

class CacheServiceTest extends TestCase
{
    private ?CacheService $cacheService;
    private MockObject|CacheSystemInterface $cacheSystemMock;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->cacheSystemMock = $this->createMock(CacheSystemInterface::class);
        $this->cacheService = new CacheService($this->cacheSystemMock, 'test_namespace', true);
    }

    /**
     * @throws ReflectionException
     */
    public function testConstructorInitializesCache(): void
    {
        $cacheStructure = [
            'key1' => [(object)['property1' => 'data1']],
            'key2' => [(object)['property1' => 'data2']]
        ];

        $invokedCount = $this->exactly(2);
        $this->cacheSystemMock->expects($this->once())->method('loadAllKeys')->willReturn(['key1', 'key2']);
        $this->cacheSystemMock->expects($invokedCount)->method('getByKey')
            ->willReturnCallback(function($parameters) use(&$invokedCount) {
               return match ($invokedCount->numberOfInvocations()) {
                    1 => [(object)['property1' => 'data1']],
                    2 => [(object)['property1' => 'data2']]
                };
        });

        $this->cacheService = new CacheService($this->cacheSystemMock, 'reinitialized_namespace', false);
        $cached = ReflectionUtility::getPrivateProperty($this->cacheService, 'cache');
        $this->assertSame(array_keys($cacheStructure), array_keys($cached));
        foreach ($cached as $key => $stored) {
            foreach ($stored as $index => $object) {
                $this->assertIsObject($object);
                $this->assertSame($object->property1, $cacheStructure[$key][$index]->property1);
            }
        }
    }

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     * @throws ReflectionException
     */
    public function testSaveCollectionInCacheWithSuccessful(): void
    {
        $entityMock1 = $this->createMock(BasicEntityInterface::class);
        $entityMock1->method('getId')->willReturn(111);
        $entityMock2 = $this->createMock(BasicEntityInterface::class);
        $entityMock2->method('getId')->willReturn(222);

        $entities = [$entityMock1, $entityMock2];
        $parameters = ['sampleEntities' => 'BasicEntityInterface'];

        $this->cacheService->saveCollectionInCache($entities, $parameters);

        $cachedEntities = $this->cacheService->getCachedCollectionByParameters($parameters);
        foreach ($cachedEntities as $key => $cachedEntity) {
            $this->assertSame($entities[$key]->getId(), $cachedEntity->getId());
        }
    }

    /**
     * @throws Exception
     */
    public function testSaveCollectionInCacheThrowsExceptionForMultipleRecordsAndSingleFlag(): void
    {
        $entityMock1 = $this->createMock(BasicEntityInterface::class);
        $entityMock2 = $this->createMock(BasicEntityInterface::class);

        $entities = [$entityMock1, $entityMock2];
        $parameters = ['key' => 'value'];
        $flags = [CacheService::SINGLE_NEWEST_FLAG];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/^More than one record/');

        $this->cacheService->saveCollectionInCache($entities, $parameters, $flags);
    }

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function testSaveSingleRecordInCache(): void
    {
        $entityMock = $this->createMock(BasicEntityInterface::class);
        $entityMock->method('getId')->willReturn(456);
        $parameters = ['key' => 'value'];

        $this->cacheService->saveSingleRecordInCache($entityMock, $parameters);
        $cachedEntity = $this->cacheService->getCachedRecordByParameters($parameters);

        $this->assertSame($entityMock->getId(), $cachedEntity->getId());
    }

    public function testGetCursorCreatesNewCursor(): void
    {
        $properties = ['property', 'value'];
        $flags = [CacheService::ALL_FLAG];

        $cursor = $this->cacheService->getCursor($properties, $flags);
        $this->assertInstanceOf(CacheServiceCursor::class, $cursor);
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function testDestructorSavesCache(): void
    {
        $entity = $this->createMock(BasicEntityInterface::class);
        ReflectionUtility::setPrivateProperty($this->cacheService, 'cache', ['key1' => [$entity]]);

        $this->cacheSystemMock->expects($this->once())->method('saveByKey')->with('key1', [$entity]);
        $this->cacheSystemMock->expects($this->once())->method('storeAllKeys')->with(['key1']);

        $this->cacheService = null;
        gc_collect_cycles(); // Needed to force destructor
    }
}
