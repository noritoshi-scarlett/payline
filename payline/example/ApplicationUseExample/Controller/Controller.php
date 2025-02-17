<?php
declare(strict_types=1);

namespace Payline\Example\ApplicationUseExample\Controller;

use Money\Currency;
use Money\Money;
use Payline\Example\EntityExample\Core\Order;
use Payline\Example\EntityExample\Core\OrderPrice;
use Payline\Example\EntityExample\MoneyHubEntity;
use Payline\Example\EntityExample\OrderEntity;
use Payline\Example\EntityExample\PaymentLogEnum;
use Payline\App\Application\Exception\InvalidArgumentException;
use Payline\App\Application\Exception\InvalidLogStateEnumException;
use Payline\App\Application\Manager\RelatedEntityCollectionLogsManager;
use Payline\App\Domain\Model\RelatedEntityCollection;
use Payline\App\Interface\Entity\DataHubEntity\DataHubEntityInterface;
use Payline\App\Interface\Entity\RelatedEntity\RelatedEntityInterface;
use Payline\Example\EntityExample\Source;

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
            fn(iterable $orderPriceList): DataHubEntityInterface => new MoneyHubEntity(Money::sum(...$orderPriceList))
        );

        $source = new Source(1, 'Platnosc-ZBZIK-owana');
        $state = PaymentLogEnum::PENDING;

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

        if ($this->relatedEntityCollectionLogsManager->isStateAllowedForNextLog($source, $orderCollection, PaymentLogEnum::APPROVED)) {
            echo sprintf('State is allowed for next log, so successfully inserted log for %s', $state->value);
        } else {
            echo sprintf('State is not allowed for next log, so log for %s was not correctly inserted', $state->value);
        }
    }
}
