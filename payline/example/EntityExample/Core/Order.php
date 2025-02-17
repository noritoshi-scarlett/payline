<?php

namespace Payline\Example\EntityExample\Core;

class Order
{
    public function __construct(
        private readonly int        $id,
        private readonly array      $customer,
        private readonly OrderPrice $orderPrice,
        private array               $items = [],
    )
    {
    }
    public function getId(): int
    {
        return $this->id;
    }

    public function getCustomer(): array
    {
        return $this->customer;
    }

    public function getOrderPrice(): OrderPrice
    {
        return $this->orderPrice;
    }

    public function addItem($item): void
    {
        $this->items[] = $item;
    }

    public function getItems(): array
    {
        return $this->items;
    }
}
