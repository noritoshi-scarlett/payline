<?php
declare(strict_types=1);

namespace Noritoshi\Payline\Test\Infrastructure\Library\Cache;

use Noritoshi\Payline\Infrastructure\Library\Cache\RedisCacheSystem;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Redis;

class RedisCacheSystemTest extends TestCase
{
    private Redis $redisMock;
    private RedisCacheSystem $cacheSystem;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->redisMock = $this->createMock(Redis::class);
        $this->redisMock->method('connect')->willReturn(true);

        $this->cacheSystem = new RedisCacheSystem($this->redisMock, 'testhost', 1234);
    }

    public function testIsConnectedReturnsTrueOnSuccessfulPing(): void
    {
        $this->redisMock->method('ping')->willReturn(true);
        $this->assertTrue($this->cacheSystem->isConnected());
    }

    public function testIsConnectedReturnsFalseOnPingFailure(): void
    {
        $this->redisMock->method('ping')->willThrowException(new \Exception());
        $this->assertFalse($this->cacheSystem->isConnected());
    }

    public function testLoadAllKeysReturnsArray(): void
    {
        $data = ['key1', 'key2'];
        $this->redisMock->method('get')->with('__ALL_KEYS__')->willReturn(serialize($data));
        $this->assertSame($data, $this->cacheSystem->loadAllKeys());
    }

    public function testStoreAllKeysSavesKeysSuccessfully(): void
    {
        $keys = ['key1', 'key2'];
        $this->redisMock->method('set')->with('__ALL_KEYS__', serialize($keys))->willReturn(true);
        $this->assertTrue($this->cacheSystem->storeAllKeys($keys));
    }

    public function testGetByKeyReturnsObjectWhenKeyExists(): void
    {
        $data = ['key' => 'value'];
        $this->redisMock->method('get')->with('testKey')->willReturn(serialize($data));
        $this->assertEquals($data, $this->cacheSystem->getByKey('testKey'));
    }

    public function testGetByKeyReturnsNullWhenKeyDoesNotExist(): void
    {
        $this->redisMock->method('get')->with('testKey')->willReturn(false);
        $this->assertNull($this->cacheSystem->getByKey('testKey'));
    }

    public function testSaveByKeyReturnsTrueOnSuccessfulSave(): void
    {
        $data = ['key' => 'value'];
        $this->redisMock->method('set')->with('testKey', serialize($data))->willReturn(true);
        $this->assertTrue($this->cacheSystem->saveByKey('testKey', $data));
    }

    public function testSaveByKeyReturnsFalseOnSaveFailure(): void
    {
        $data = ['key' => 'value'];
        $this->redisMock->method('set')->with('testKey', serialize($data))->willReturn(false);
        $this->assertFalse($this->cacheSystem->saveByKey('testKey', $data));
    }
}
