<?php
declare(strict_types=1);

namespace Noritoshi\Payline\Infrastructure\Library\Cache;

use Redis;

class RedisCacheSystem implements CacheSystemInterface
{
    private const string ALL_KEYS = '__ALL_KEYS__';

    public function __construct(
        private ?Redis $redis,
        string $host,
        int    $port
    )
    {
        $this->redis =$redis ?? new Redis();

        if (!$this->redis->connect($host, $port)) {
            throw new \RuntimeException('Unable to connect to Redis server');
        }
    }

    public function isConnected(): bool
    {
        try {
            return (bool)$this->redis->ping();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function loadAllKeys(): array
    {
        return (array)$this->getByKey(self::ALL_KEYS);
    }

    public function storeAllKeys(array $keys): bool
    {
        return $this->saveByKey(self::ALL_KEYS, $keys);
    }

    public function getByKey(string $key): array|null
    {
        $data = $this->redis->get($key);
        if ($data === false) {
            return null;
        }

        return unserialize($data);
    }

    public function saveByKey(string $key, array $data): bool
    {
        $result = $this->redis->set($key, serialize($data));

        return match (true) {
            $result === true, $result === "OK" => true,
            is_object($result) && method_exists($result, 'getPayload') && $result->getPayload() === 'OK' => true,
            default => false,
        };
    }
}
