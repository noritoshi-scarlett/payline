<?php
declare(strict_types=1);

namespace Noritoshi\Payline\Test\Domain\Entity\RelatedEntityCollection;

use Noritoshi\Payline\Domain\Entity\RelatedEntityCollection\RelatedEntityCollection;
use Noritoshi\Payline\Interface\Entity\DataHubEntity\DataHubEntityInterface;
use Noritoshi\Payline\Interface\Entity\RelatedEntity\RelatedEntityInterface;
use Noritoshi\Payline\Test\TestDataProviders\EntityProvider;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class RelatedEntityCollectionTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testHasRelatedEntityReturnsTrueWhenEntityExists(): void
    {
        $mockEntity1 = EntityProvider::getRelatedEntityMock($this->createMock(RelatedEntityInterface::class), 1);
        $mockEntity2 = EntityProvider::getRelatedEntityMock($this->createMock(RelatedEntityInterface::class), 2);

        $relatedEntityCollection = new RelatedEntityCollection(1, [$mockEntity1, $mockEntity2]);

        $this->assertSame($relatedEntityCollection->getId(), 1);
        $this->assertSame($relatedEntityCollection->getRelatedEntities(), [$mockEntity1, $mockEntity2]);
        $this->assertTrue($relatedEntityCollection->hasRelatedEntity($mockEntity1));
    }

    /**
     * @throws Exception
     */
    public function testHasRelatedEntityReturnsFalseWhenEntityDoesNotExist(): void
    {
        $mockEntity1 = EntityProvider::getRelatedEntityMock($this->createMock(RelatedEntityInterface::class), 1);
        $mockEntity2 = EntityProvider::getRelatedEntityMock($this->createMock(RelatedEntityInterface::class), 2);
        $mockEntity3 = EntityProvider::getRelatedEntityMock($this->createMock(RelatedEntityInterface::class), 3);

        $relatedEntityCollection = new RelatedEntityCollection(1, [$mockEntity1, $mockEntity2]);

        $this->assertFalse($relatedEntityCollection->hasRelatedEntity($mockEntity3));
    }

    /**
     * @throws Exception
     */
    public function testCountRelatedEntities(): void
    {
        $mockEntity1 = $this->createMock(RelatedEntityInterface::class);
        $mockEntity2 = $this->createMock(RelatedEntityInterface::class);

        $relatedEntityCollection = new RelatedEntityCollection(1, [$mockEntity1, $mockEntity2]);

        $this->assertSame(2, $relatedEntityCollection->countRelatedEntities());
    }

    /**
     * @throws Exception
     */
    public function testGetRelatedEntity(): void
    {
        $mockEntity1 = $this->createMock(RelatedEntityInterface::class);
        $mockEntity2 = $this->createMock(RelatedEntityInterface::class);

        $relatedEntityCollection = new RelatedEntityCollection(1, [$mockEntity1, $mockEntity2]);

        $this->assertSame($mockEntity1, $relatedEntityCollection->getRelatedEntity(0));
        $this->assertSame($mockEntity2, $relatedEntityCollection->getRelatedEntity(1));
    }

    /**
     * @throws Exception
     */
    public function testGetRelatedEntityThrowsExceptionForInvalidIndex(): void
    {
        $mockEntity1 = $this->createMock(RelatedEntityInterface::class);

        $relatedEntityCollection = new RelatedEntityCollection(1, [$mockEntity1]);

        $this->expectException(\Error::class);
        $relatedEntityCollection->getRelatedEntity(5);
    }

    /**
     * @throws Exception
     */
    public function testCalculateDataHub(): void
    {
        $mockEntity1 = EntityProvider::getRelatedEntityMock($this->createMock(RelatedEntityInterface::class), 1, (object)['amount' => 5000]);
        $mockEntity2 = EntityProvider::getRelatedEntityMock($this->createMock(RelatedEntityInterface::class), 3, (object)['amount' => 9000]);
        $relatedEntityCollection = new RelatedEntityCollection(1, [$mockEntity1, $mockEntity2]);

        $providerForSingleData = fn(RelatedEntityInterface $entity):int => $entity->getCoreEntity()->amount;
        /**
         * @param iterable<int> $data
         * @return DataHubEntityInterface<int>
         * @throws Exception
         */
        $enumeratorForDataCollection = function (iterable $data):DataHubEntityInterface {
            $sum = array_reduce($data, fn($carry, int $obj) => ($carry ?? 0) + $obj);
            $mockDataHub = $this->createMock(DataHubEntityInterface::class);
            $mockDataHub->method('getDataObject')->willReturn($sum);
            return $mockDataHub;
        };

        $dataHub = $relatedEntityCollection->setDataHubByCalculation($providerForSingleData, $enumeratorForDataCollection);

        $this->assertSame(14000, $dataHub->getDataObject());
    }
}