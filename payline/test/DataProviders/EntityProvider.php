<?php
declare(strict_types=1);

namespace Payline\Test\DataProviders;

use Payline\App\Infrastructure\Domain\BasicEntityInterface;
use Payline\App\Interface\Entity\LogEntity\LogEntityInterface;
use PHPUnit\Framework\MockObject\MockObject;

class EntityProvider
{
    public static function createLogEntityMock(LogEntityInterface&MockObject $mock, int $id, \DateTimeImmutable $createdAt): LogEntityInterface
    {
        $mock->method('getId')->willReturn($id);
        $mock->method('getCreatedAt')->willReturn($createdAt);
        return $mock;
    }

    public static function createBasicEntityMock(BasicEntityInterface&MockObject $mock, int $id): BasicEntityInterface
    {
        $mock->method('getId')->willReturn($id);
        return $mock;
    }
}