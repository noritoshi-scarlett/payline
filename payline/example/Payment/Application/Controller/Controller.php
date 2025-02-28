<?php
declare(strict_types=1);

namespace Noritoshi\Payline\Example\Payment\Application\Controller;

use Money\Currency;
use Money\Money;
use Noritoshi\Payline\Application\Exception\InvalidArgumentException;
use Noritoshi\Payline\Application\Exception\Validation\InvalidDateException;
use Noritoshi\Payline\Application\Exception\Validation\InvalidLogStateEnumException;
use Noritoshi\Payline\Application\Factory\CacheServiceFactory;
use Noritoshi\Payline\Application\Manager\RelatedEntityCollectionLogsManager;
use Noritoshi\Payline\Domain\Entity\RelatedEntityCollection\RelatedEntityCollection;
use Noritoshi\Payline\Interface\Entity\DataHubEntity\DataHubEntityInterface;
use Noritoshi\Payline\Interface\Entity\RelatedEntity\RelatedEntityInterface;
use Noritoshi\Payline\Example\Domain\Repository\LogRepository;
use Noritoshi\Payline\Example\Order\Domain\Entity\Order;
use Noritoshi\Payline\Example\Order\Domain\Entity\OrderPrice;
use Noritoshi\Payline\Example\Payment\Application\Factory\PaymentLogFactory;
use Noritoshi\Payline\Example\Payment\Domain\Entity\MoneyHubEntity;
use Noritoshi\Payline\Example\Payment\Domain\Entity\OrderEntity;
use Noritoshi\Payline\Example\Payment\Domain\Entity\PaymentLog;
use Noritoshi\Payline\Example\Payment\Domain\Entity\PaymentSource;
use Noritoshi\Payline\Example\Payment\Plugin\PayU\Domain\Entity\PayUPaymentLogEnum;

class Controller
{
    public function __construct(
        private readonly LogRepository $logRepository,
        private readonly PaymentLogFactory $logFactory,
        private readonly CacheServiceFactory $cacheServiceFactory,
    )
    {
    }

    public function addNewLogForSingleOrder(): void
    {
        $orderOne = new Order(1, ['name' => 'test'], new OrderPrice(
            new Money(100, new Currency('USD')),
            10
        ));
        $orderTwo = new Order(2, ['name' => 'test'], new OrderPrice(
            new Money(140, new Currency('USD')),
        ));

        /** @var RelatedEntityCollection<Order> $orderCollection */
        $orderCollection = new RelatedEntityCollection(
            1,
            [
                new OrderEntity(1, $orderOne),
                new OrderEntity(2, $orderTwo)
            ],
        );

        $orderCollection->setDataHubByCalculation(
            /**
             * @param RelatedEntityInterface<Order> $relatedEntity
             */
            fn(RelatedEntityInterface $relatedEntity): Money => $relatedEntity->getCoreEntity()->getOrderPrice()->discountPrice,
            /**
             * @param iterable<Money> $orderPriceList
             * @return MoneyHubEntity<Money>
             */
            fn(iterable $orderPriceList): DataHubEntityInterface => new MoneyHubEntity(1, Money::sum(...$orderPriceList))
        );

        $source = new PaymentSource(1, 'Platnosc-ZBZIK-owana');

        /** @var RelatedEntityCollectionLogsManager<PaymentLog<Order, Money>> $relatedEntityCollectionLogsManager */
        $relatedEntityCollectionLogsManager = new RelatedEntityCollectionLogsManager(
            $this->logRepository,
            $this->logFactory,
            PaymentLog::class,
            $this->cacheServiceFactory
        );

        try {
            $log = $relatedEntityCollectionLogsManager->createLog(
                $source,
                $orderCollection,
                PayUPaymentLogEnum::PENDING,
                'New log with PENDING for single order',
                new \DateTimeImmutable()
            );
            /** @var Order $coreEntity */
            $coreEntity = $log->getRelatedEntityCollection()->getRelatedEntity(0)->getCoreEntity();
            echo 'Price: ' . $coreEntity->getOrderPrice()->discountPrice->getAmount() . PHP_EOL;
        } catch (InvalidLogStateEnumException|InvalidArgumentException|InvalidDateException $e) {
            echo 'Error message: ' . $e->getMessage() . PHP_EOL;
        }


        try {
            $log = $relatedEntityCollectionLogsManager->createLog(
                $source,
                $orderCollection,
                PayUPaymentLogEnum::NEW,
                'New log with NEW for single order',
                new \DateTimeImmutable()
            );
            /** @var Order $coreEntity */
            $coreEntity = $log->getRelatedEntityCollection()->getRelatedEntity(0)->getCoreEntity();
            echo 'Price: ' . $coreEntity->getOrderPrice()->discountPrice->getAmount() . PHP_EOL;
        } catch (InvalidLogStateEnumException|InvalidArgumentException|InvalidDateException $e) {
            echo 'Error message: ' . $e->getMessage() . PHP_EOL;
        }


    }
}
