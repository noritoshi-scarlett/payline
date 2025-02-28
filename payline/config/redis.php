<?php
declare(strict_types=1);

use Noritoshi\Payline\Infrastructure\Library\Cache\RedisCacheSystem;

class RedisFactory
{
    public static function create(): RedisCacheSystem
    {
        $host = getenv('REDIS_HOST') ?: ($_ENV['REDIS_HOST'] ?? 'redis');
        $port = (int) (getenv('REDIS_PORT') ?: ($_ENV['REDIS_PORT'] ?? 6379));

        return new RedisCacheSystem(new Redis(), $host, $port);
    }
}
