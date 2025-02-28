<?php
declare(strict_types=1);

namespace Noritoshi\Payline\Test\Application\Utility\Validator;

use Noritoshi\Payline\Application\Exception\Validation\InvalidDateException;
use Noritoshi\Payline\Application\Exception\Validation\InvalidLogStateEnumException;
use Noritoshi\Payline\Application\Utility\Validator\LogValidator;
use Noritoshi\Payline\Interface\Entity\LogEntity\LogEntityInterface;
use Noritoshi\Payline\Interface\Entity\Source\SourceInterface;
use Noritoshi\Payline\Test\TestDataProviders\EntityProvider;
use Noritoshi\Payline\Test\TestDataProviders\SampleStateEnum;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class LogValidatorTest extends TestCase
{
    /**
     * @throws InvalidDateException
     * @throws Exception
     * @throws InvalidLogStateEnumException
     */
    public function testValidationSucceeds(): void
    {
        $latestLogMock = EntityProvider::createLogEntityMock(
            $this->createMock(LogEntityInterface::class),
            1,
            new \DateTimeImmutable('2023-10-10 10:00:00.0'),
            SampleStateEnum::INITIALIZED
        );

        $sourceMock = $this->createMock(SourceInterface::class);
        $sourceMock->method('isStateAllowedForNextLog')->willReturn(true);

        $this->assertTrue(LogValidator::newLogDataCompareToLatestLog(
            $latestLogMock,
            $sourceMock,
            SampleStateEnum::PROCESSING,
            new \DateTimeImmutable('2023-10-10 10:00:00.1234')
        ));
    }

    /**
     * @throws InvalidDateException
     * @throws Exception
     */
    public function testValidationFailsOnInvalidState(): void
    {
        $latestLogMock = EntityProvider::createLogEntityMock(
            $this->createMock(LogEntityInterface::class),
            1,
            new \DateTimeImmutable('2023-10-10 10:00:00.0'),
            SampleStateEnum::INITIALIZED
        );

        $sourceMock = $this->createMock(SourceInterface::class);
        $sourceMock->method('isStateAllowedForNextLog')->willReturn(false);

        $this->expectException(InvalidLogStateEnumException::class);
        $this->expectExceptionMessage('State is not allowed.');

        LogValidator::newLogDataCompareToLatestLog(
            $latestLogMock,
            $sourceMock,
            SampleStateEnum::PROCESSING,
            new \DateTimeImmutable('2023-10-10 10:01:00')
        );
    }

    /**
     * @throws Exception
     * @throws InvalidDateException
     * @throws InvalidLogStateEnumException
     * @throws \DateMalformedStringException
     */
    #[DataProvider('providerForInvalidDates')]
    public function testValidationFailsOnInvalidDate(string $latest, string $new): void
    {
        $latestLogMock = EntityProvider::createLogEntityMock(
            $this->createMock(LogEntityInterface::class),
            1,
            new \DateTimeImmutable($latest),
            SampleStateEnum::INITIALIZED
        );

        $sourceMock = $this->createMock(SourceInterface::class);
        $sourceMock->method('isStateAllowedForNextLog')->willReturn(true);

        $this->expectException(InvalidDateException::class);
        $this->expectExceptionMessage('Date is too old.');

        LogValidator::newLogDataCompareToLatestLog(
            $latestLogMock,
            $sourceMock,
            SampleStateEnum::PROCESSING,
            new \DateTimeImmutable($new)
        );
    }

    /**
     * @return array<array<string>> With pairs: latest|new
     */
    public static function providerForInvalidDates(): array
    {
        return [
            ['2023-10-10 09:59:59', '2023-10-10 09:59:58'],
            ['2023-10-10 09:59:59.1234', '2023-10-10 09:59:59.0'],
            ['2023-10-10 09:59:59.7890', '2023-10-10 09:59:59.1234'],
            ['2023-10-10 10:00:00', '2023-10-10 09:59:59.9999']
        ];
    }
}
