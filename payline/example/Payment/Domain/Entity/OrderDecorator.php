<?php
declare(strict_types=1);

namespace Noritoshi\Payline\Example\Payment\Domain\Entity;

use Noritoshi\Payline\Example\Order\Domain\Entity\Order;
use Noritoshi\Payline\Interface\Entity\RelatedEntity\RelatedEntityInterface;

/**
 * @template-implements RelatedEntityInterface<Order>
 */
readonly class OrderDecorator implements RelatedEntityInterface
{
    public function __construct(
        private int    $id,
        private Order $coreEntity
    )
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Order
     */
    public function getCoreEntity(): object
    {
        return $this->coreEntity;
    }
}