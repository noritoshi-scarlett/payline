<?php
declare(strict_types=1);

namespace Noritoshi\Payline\Test\TestDataProviders;

use Noritoshi\Payline\Infrastructure\Domain\BasicEntityInterface;
use Noritoshi\Payline\Interface\Entity\LogEntity\LogEntityInterface;
use Noritoshi\Payline\Interface\Entity\LogEntity\StateEnum\StateEnumInterface;
use Noritoshi\Payline\Interface\Entity\RelatedEntity\RelatedEntityInterface;
use PHPUnit\Framework\MockObject\MockObject;

class EntityProvider
{
    public static function createLogEntityMock(
        LogEntityInterface&MockObject $mock,
        int $id,
        \DateTimeImmutable $createdAt,
        ?StateEnumInterface $state = null
    ): LogEntityInterface&MockObject
    {
        $mock->method('getId')->willReturn($id);
        $mock->method('getCreatedAt')->willReturn($createdAt);
        if (isset($state)) {
            $mock->method('getState')->willReturn($state);
        }
        return $mock;
    }

    public static function createBasicEntityMock(
        BasicEntityInterface&MockObject $mock,
        int $id
    ): BasicEntityInterface&MockObject
    {
        $mock->method('getId')->willReturn($id);
        return $mock;
    }

    public static function getRelatedEntityMock(
        RelatedEntityInterface&MockObject $mock,
        int $id,
        ?object $coreEntity = null
    ): RelatedEntityInterface&MockObject
    {
        $mock->method('getId')->willReturn($id);
        if (isset($coreEntity)) {
            $mock->method('getCoreEntity')->willReturn($coreEntity);
        }
        return $mock;
    }
}
