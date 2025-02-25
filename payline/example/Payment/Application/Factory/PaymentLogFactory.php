<?php

namespace Payline\Example\Payment\Application\Factory;

use Override;
use Payline\App\Application\Factory\LogAbstractFactory;
use Payline\App\Domain\Entity\RelatedEntityCollection\RelatedEntityCollectionInterface;
use Payline\App\Interface\Entity\LogEntity\LogEntityInterface;
use Payline\App\Interface\Entity\LogEntity\StateEnum\StateEnumInterface;
use Payline\App\Interface\Entity\Source\SourceInterface;
use Payline\Example\Payment\Domain\Entity\PaymentLog;

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