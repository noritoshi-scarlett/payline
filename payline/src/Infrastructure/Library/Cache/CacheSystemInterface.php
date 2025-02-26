<?php
declare(strict_types=1);

namespace Payline\App\Infrastructure\Library\Cache;

/**
 * @warning Set prefixIdentification before usage.
 */
interface CacheSystemInterface
{
    public string $prefixIdentification {
        get;
        set;
    }
    public function isConnected(): bool;
    public function loadAllKeys(): array;
    public function storeAllKeys(array $keys);
    public function getByKey(string $key): array|null;
    public function saveByKey(string $key, array $data): bool;
}
