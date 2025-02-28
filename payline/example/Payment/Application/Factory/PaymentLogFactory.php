<?php

namespace Noritoshi\Payline\Example\Payment\Application\Factory;

use Override;
use Noritoshi\Payline\Application\Factory\LogAbstractFactory;
use Noritoshi\Payline\Domain\Entity\RelatedEntityCollection\RelatedEntityCollectionInterface;
use Noritoshi\Payline\Interface\Entity\LogEntity\LogEntityInterface;
use Noritoshi\Payline\Interface\Entity\LogEntity\StateEnum\StateEnumInterface;
use Noritoshi\Payline\Interface\Entity\Source\SourceInterface;
use Noritoshi\Payline\Example\Payment\Domain\Entity\PaymentLog;

/**
 * @template T of object
 * @template V of object
 * @template-extends LogAbstractFactory<T, V>
 */
class PaymentLogFactory extends LogAbstractFactory
{
    #[Override]
    /**
     * @param RelatedEntityCollectionInterface<V> $relatedEntityCollection
     * @return LogEntityInterface<T, V>
     */
    public function createLogEntity(
        SourceInterface                  $source,
        RelatedEntityCollectionInterface $relatedEntityCollection,
        StateEnumInterface               $state,
        string                           $message,
        \DateTimeImmutable               $createdAt,
    ): LogEntityInterface
    {
        /** @var PaymentLog<T, V> */
        return new PaymentLog(
            1,
            $source,
            $relatedEntityCollection,
            $state,
            $createdAt,
            $message,
        );
    }
}