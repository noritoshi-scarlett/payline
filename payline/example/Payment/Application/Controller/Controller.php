<?php
declare(strict_types=1);

namespace Payline\Example\Payment\Application\Controller;

use Money\Currency;
use Money\Money;
use Payline\App\Application\Exception\InvalidArgumentException;
use Payline\App\Application\Exception\Validation\InvalidLogStateEnumException;
use Payline\App\Application\Manager\RelatedEntityCollectionLogsManager;
use Payline\App\Domain\Model\RelatedEntityCollection;
use Payline\App\Interface\Entity\DataHubEntity\DataHubEntityInterface;
use Payline\App\Interface\Entity\RelatedEntity\RelatedEntityInterface;
use Payline\Example\Order\Domain\Entity\Order;
use Payline\Example\Order\Domain\Entity\OrderPrice;
use Payline\Example\Payment\Domain\Entity\MoneyHubEntity;
use Payline\Example\Payment\Domain\Entity\OrderEntity;
use Payline\Example\Payment\Domain\Entity\PaymentSource;
use Payline\Example\Payment\Plugin\PayU\Domain\Entity\PayUPaymentLogEnum;

class Controller
{
    /**
     * @param RelatedEntityCollectionLogsManager<Money, Order> $relatedEntityCollectionLogsManager
     */
    public function __construct(
        private readonly RelatedEntityCollectionLogsManager $relatedEntityCollectionLogsManager
    )
    {
    }

    /**
     * @throws InvalidArgumentException
     */
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

        $orderCollection->calculateDataHub(
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
        $state = PayUPaymentLogEnum::PENDING;

        try {
            $log = $this->relatedEntityCollectionLogsManager->createLog(
                $source,
                $orderCollection,
                $state,
                'New log with PENDING for single order',
                new \DateTimeImmutable()
            );
            /** @var RelatedEntityInterface<Order> $firstRelatedEntity */
            $firstRelatedEntity = $log->getRelatedEntityCollection()->getRelatedEntity(0)->getCoreEntity();
            $firstRelatedEntity->getCoreEntity()->getOrderPrice();
        } catch (InvalidLogStateEnumException $e) {
            // Handle exception
        } catch (InvalidArgumentException $e) {
            // TODO
        }

    }
}
