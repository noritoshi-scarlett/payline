<?php
declare(strict_types=1);

namespace Noritoshi\Payline\Example\Payment\Domain\Entity;

use Money\Currency;
use Money\Money;
use Noritoshi\Payline\Interface\Entity\DataHubEntity\DataHubEntityInterface;

/**
 * @template-implements DataHubEntityInterface<Money>
 */
readonly class MoneyHubEntity implements DataHubEntityInterface
{
    public function __construct(
        private int    $id,
        private Money $money,
    )
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDataObject(): Money
    {
       return $this->money;
    }

    public function serializeToJson(): string
    {
        return json_encode($this->money);
    }

    public static function deserializeFromJson(string $json): Money
    {
        $data = json_decode($json, true);

        return new Money(
            $data['amount'],
            new Currency($data['currency']['code'])
        );
    }
}