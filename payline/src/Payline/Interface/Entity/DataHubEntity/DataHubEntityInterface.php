<?php
declare(strict_types=1);

namespace Noritoshi\Payline\Interface\Entity\DataHubEntity;

use Noritoshi\Payline\Infrastructure\Domain\BasicEntityInterface;

/**
 * @template T of mixed
 */
interface DataHubEntityInterface extends BasicEntityInterface
{
    /**
     * @return T
     */
    public function getDataObject(): mixed;

    public function serializeToJson(): string;

    /**
     * @return T
     */
    public static function deserializeFromJson(string $json): mixed;
}
