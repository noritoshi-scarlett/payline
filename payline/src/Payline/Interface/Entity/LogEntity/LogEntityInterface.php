<?php
declare(strict_types=1);

namespace Noritoshi\Payline\Interface\Entity\LogEntity;

use Noritoshi\Payline\Domain\Entity\RelatedEntityCollection\RelatedEntityCollectionInterface;
use Noritoshi\Payline\Infrastructure\Domain\BasicEntityInterface;
use Noritoshi\Payline\Interface\Entity\LogEntity\StateEnum\StateEnumInterface;
use Noritoshi\Payline\Interface\Entity\Source\SourceInterface;

/**
 * @template T of object
 * @template V of object
 */
interface LogEntityInterface extends BasicEntityInterface
{
    /**
     * @return RelatedEntityCollectionInterface<V, T>
     */
    public function getRelatedEntityCollection(): RelatedEntityCollectionInterface;

    public function getMessage(): string;
    public function setMessage(string $message): void;
    public function getState(): StateEnumInterface;
    public function getSource(): SourceInterface;
    public function getCreatedAt(): \DateTimeImmutable;
    public function __toString(): string;
}