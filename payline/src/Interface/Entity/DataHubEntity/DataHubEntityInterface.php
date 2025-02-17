<?php
declare(strict_types=1);

namespace Payline\App\Interface\Entity\DataHubEntity;

/**
 * @template T of object
 */
interface DataHubEntityInterface
{
    /**
     * @return T
     */
    public function getDataObject(): object;

    public function serializeToJson(): string;

    /**
     * @return T
     */
    public static function deserializeFromJson(string $json): object;
}
