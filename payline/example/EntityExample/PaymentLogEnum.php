<?php
declare(strict_types=1);

namespace Payline\Example\EntityExample;

use Payline\App\Interface\Entity\LogEntity\StateEnum\StateEnumInterface;

enum PaymentLogEnum: string implements StateEnumInterface
{
    case NEW = 'NEW';
    case PENDING = 'PENDING';
    case APPROVED = 'APPROVED';
    case REJECTED = 'REJECTED';
    case CANCELED = 'CANCELED';
    case COMPLETED = 'COMPLETED';

    public function getInitializeStates(): array
    {
        return [self::NEW];
    }

    public function getFinalStates(): array
    {
        return [self::CANCELED, self::COMPLETED];
    }

    public function getGraph(): array
    {
        return [
            self::NEW->name => [self::PENDING, self::CANCELED],
            self::PENDING->name => [self::APPROVED, self::REJECTED, self::CANCELED],
            self::APPROVED->name => [self::COMPLETED, self::CANCELED],
            self::REJECTED->name => [self::CANCELED],
            self::CANCELED->name => [],
            self::COMPLETED->name => [],
        ];
    }
}
