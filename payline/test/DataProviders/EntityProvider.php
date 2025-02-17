<?php

namespace Payline\Test\DataProviders;

use Payline\App\Interface\Entity\BasicEntityInterface;
use Payline\App\Interface\Entity\LogEntity\LogEntityInterface;
use Payline\Test\Application\Library\EntitySorterTest;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\Generator\Generator;
use PHPUnit\Framework\TestCase;

class EntityProvider
{
    public static function createLogEntityMock($mock, int $id, \DateTimeImmutable $createdAt): LogEntityInterface
    {
        $mock->method('getId')->willReturn($id);
        $mock->method('getCreatedAt')->willReturn($createdAt);
        return $mock;

    }

    public static function createBasicEntityMock($mock, int $id): BasicEntityInterface
    {
        $mock->method('getId')->willReturn($id);
        return $mock;
    }
}