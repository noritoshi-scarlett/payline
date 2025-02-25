<?php
declare(strict_types=1);

namespace Payline\Test\Application\Utility;

use Payline\App\Application\Exception\InvalidArgumentException;
use Payline\App\Application\Utility\Sorter\EntitySorter;
use Payline\App\Application\Utility\Sorter\SortDirectionEnum;
use Payline\App\Infrastructure\Domain\BasicEntityInterface;
use Payline\App\Interface\Entity\LogEntity\LogEntityInterface;
use Payline\Test\DataProviders\EntityProvider;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class EntitySorterTest extends TestCase
{
    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testSortByDate(): void
    {
        $entity1 = $this->createLogEntityMock(1, new \DateTimeImmutable('2021-01-01 00:00:00'));
        $entity2 = $this->createLogEntityMock(2, new \DateTimeImmutable('2021-01-09 00:00:00'));
        $entity3 = $this->createLogEntityMock(3, new \DateTimeImmutable('2021-01-05 00:00:00'));
        $entity4 = $this->createLogEntityMock(4, new \DateTimeImmutable('2021-01-03 00:00:00'));

        $expectedForDescSort = [$entity2, $entity3, $entity4, $entity1];
        $expectedForAscSort = [$entity1, $entity4, $entity3, $entity2];

        $sorted = EntitySorter::sortByDate([$entity1, $entity2, $entity3, $entity4], SortDirectionEnum::DESCENDING);
        $this->assertSame($expectedForDescSort, $sorted);

        $sorted = EntitySorter::sortByDate([$entity1, $entity2, $entity3, $entity4], SortDirectionEnum::ASCENDING);
        $this->assertSame($expectedForAscSort, $sorted);
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testDefaultSortById(): void
    {
        $entity1 = $this->createBasicEntityMock(5);
        $entity2 = $this->createBasicEntityMock(9);
        $entity3 = $this->createBasicEntityMock(2);
        $entity4 = $this->createBasicEntityMock(6);

        $expectedForAscSort = [$entity3, $entity1, $entity4, $entity2];
        $expectedForDescSort = [$entity2, $entity4, $entity1, $entity3];

        $sorted = EntitySorter::sortById([$entity1, $entity2, $entity3, $entity4], SortDirectionEnum::ASCENDING);
        $this->assertSame($expectedForAscSort, $sorted);

        $sorted = EntitySorter::sortById([$entity1, $entity2, $entity3, $entity4], SortDirectionEnum::DESCENDING);
        $this->assertSame($expectedForDescSort, $sorted);
    }

    /**
     * @throws Exception
     */
    public function createLogEntityMock(int $id, \DateTimeImmutable $createdAt): LogEntityInterface
    {
        return EntityProvider::createLogEntityMock(
            $this->createMock(LogEntityInterface::class),
            $id,
            $createdAt
        );

    }

    /**
     * @throws Exception
     */
    public function createBasicEntityMock(int $id): BasicEntityInterface
    {
        return EntityProvider::createBasicEntityMock(
            $this->createMock(BasicEntityInterface::class),
            $id
        );
    }
}
