<?php
declare(strict_types=1);

namespace Payline\Example\EntityExample;

use Payline\App\Interface\Entity\DataHubEntity\DataHubEntityInterface;
use Money\Currency;
use Money\Money;

/**
 * @template T of object for $money representation.
 */
class MoneyHubEntity implements DataHubEntityInterface
{
    /**
     * @param T $money
     */
    public function __construct(
        private readonly object $money,
    )
    {
    }

    /**
     * @return T
     */
    public function getDataObject(): object
    {
       return $this->money;
    }

    public function serializeToJson(): string
    {
        return json_encode($this->money);
    }

    /**
     * @return T
     */
    public static function deserializeFromJson(string $json): object
    {
        $data = json_decode($json, true);

        return new Money($data['amount'], new Currency($data['currency']['code']));
    }
}