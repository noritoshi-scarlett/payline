<?php
declare(strict_types=1);

namespace Payline\Example\EntityExample\Core;

use Money\Money;

class OrderPrice
{
    private(set) Money $discountPrice;

    public function __construct(
        private(set) readonly Money $originalPrice,
        float $discountByAmount = 0.0,
    )
    {
        $this->discountPrice = clone $this->originalPrice;
        $this->rechargeDiscountPriceBy($discountByAmount);
    }

    public function rechargeDiscountPriceBy(float $discountPriceAmount): void
    {
        $this->discountPrice = $this->discountPrice->subtract(
            new Money($discountPriceAmount, $this->originalPrice->getCurrency())
        );
    }

    public function resetDiscountPrice(): void
    {
        $this->discountPrice = clone $this->originalPrice;
    }

    public function haveDiscount(): bool
    {
        return $this->discountPrice->getAmount() <> $this->originalPrice->getAmount();
    }
}