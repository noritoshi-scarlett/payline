<?php
declare(strict_types=1);

namespace Payline\Example\Payment\Domain\Entity;

use Payline\App\Domain\Entity\RelatedEntityCollection\RelatedEntityCollectionInterface;
use Payline\App\Interface\Entity\LogEntity\LogEntityInterface;
use Payline\App\Interface\Entity\LogEntity\StateEnum\StateEnumInterface;
use Payline\App\Interface\Entity\Source\SourceInterface;
use Payline\Example\Payment\Plugin\PayU\Domain\Entity\PayUPaymentLogEnum;

/**
 * @template T of object for DataHubEntityInterface
 * @template V of object for RelatedEntityCollectionInterface
 * @template-implements LogEntityInterface<T, V>
 */
class PaymentLog implements LogEntityInterface
{
    /**
     * @param RelatedEntityCollectionInterface<V> $relatedEntityCollection
     */
    public function __construct(
        private readonly int                              $id,
        private readonly SourceInterface                  $source,
        private readonly RelatedEntityCollectionInterface $relatedEntityCollection,
        private readonly PayUPaymentLogEnum               $state,
        private readonly \DateTimeImmutable               $createdAt,
        private ?string                                   $message = null,
    )
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return RelatedEntityCollectionInterface<V, T>
     */
    public function getRelatedEntityCollection(): RelatedEntityCollectionInterface
    {
        return $this->relatedEntityCollection;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function getState(): StateEnumInterface
    {
        return $this->state;
    }

    public function getSource(): SourceInterface
    {
        return $this->source;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function __toString(): string
    {
        return sprintf(
            'Log #%d: %s [state: %s, source: %s] @ %s',
            $this->id,
            $this->message,
            $this->state->value,
            $this->source->getName(),
            $this->createdAt->format('Y-m-d H:i:s')
        );
    }
}
