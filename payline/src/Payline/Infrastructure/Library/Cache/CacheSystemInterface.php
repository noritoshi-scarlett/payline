<?php
declare(strict_types=1);

namespace Noritoshi\Payline\Infrastructure\Library\Cache;

interface CacheSystemInterface
{
    public function isConnected(): bool;
    public function loadAllKeys(): array;
    public function storeAllKeys(array $keys);
    public function getByKey(string $key): array|null;
    public function saveByKey(string $key, array $data): bool;
}
