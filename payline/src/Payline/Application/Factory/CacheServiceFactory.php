<?php
declare(strict_types=1);

namespace Noritoshi\Payline\Application\Factory;

use Noritoshi\Payline\Infrastructure\Library\Cache\CacheSystemInterface;
use Noritoshi\Payline\Infrastructure\Library\Cache\Entity\CacheService;

class CacheServiceFactory
{
    private CacheSystemInterface $cacheSystem;

    private array $instances = [];

    public function __construct(CacheSystemInterface $cacheSystem)
    {
        $this->cacheSystem = $cacheSystem;
    }

    /**
     * @template EntityType
     * @param class-string<EntityType> $namespace
     * @return CacheService<EntityType>
     */
    public function create(string $namespace, bool $autosave): CacheService
    {
        if (false === isset($this->instances[$namespace])) {
            /** @var CacheService<EntityType> $cacheService */
            $cacheService = new CacheService($this->cacheSystem, $namespace, $autosave);
            $this->instances[$namespace] = $cacheService;
        }

        return $this->instances[$namespace];
    }
}
