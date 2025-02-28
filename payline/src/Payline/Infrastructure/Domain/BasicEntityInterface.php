<?php
declare(strict_types=1);

namespace Noritoshi\Payline\Infrastructure\Domain;

interface BasicEntityInterface
{
    public function getId(): int;
}