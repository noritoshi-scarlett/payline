<?php
declare(strict_types=1);

namespace Payline\App\Interface\Entity\LogEntity;

use Payline\App\Domain\Entity\RelatedEntityCollection\RelatedEntityCollectionInterface;
use Payline\App\Infrastructure\Domain\BasicEntityInterface;
use Payline\App\Interface\Entity\LogEntity\StateEnum\StateEnumInterface;
use Payline\App\Interface\Entity\Source\SourceInterface;

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