<?php
declare(strict_types=1);

namespace Payline\Test\Application\Library;

use Payline\App\Application\Exception\InvalidArgumentException;
use Payline\App\Application\Library\EntitySorter;
use Payline\App\Interface\Entity\BasicEntityInterface;
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
        $entities = [
            $this->createLogEntityMock(3, new \DateTimeImmutable('2021-01-03 00:00:00')),
            $this->createLogEntityMock(1, new \DateTimeImmutable('2021-01-01 00:00:00')),
            $this->createLogEntityMock(2, new \DateTimeImmutable('2021-01-04 00:00:00')),
        ];

        $expected = [
            $entities[1],
            $entities[0],
            $entities[2]
        ];

        EntitySorter::sortByDate($entities);

        $this->assertEquals($expected, $entities);
    }

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function testDefaultSortById(): void
    {
        $entities = [
            $this->createBasicEntityMock(5),
            $this->createBasicEntityMock(9),
            $this->createBasicEntityMock(2)
        ];

        $expected = [
            $entities[2],
            $entities[0],
            $entities[1]
        ];

        EntitySorter::sortByDate($entities);

        $this->assertEquals($expected, $entities);
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
