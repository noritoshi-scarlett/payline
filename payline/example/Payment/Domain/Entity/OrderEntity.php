<?php
declare(strict_types=1);

namespace Payline\Example\Payment\Domain\Entity;

use Payline\App\Interface\Entity\RelatedEntity\RelatedEntityInterface;

/**
 * @template V of object
 * @template-implements RelatedEntityInterface<V>
 */
readonly class OrderEntity implements RelatedEntityInterface
{
    /**
     * @param V $coreEntity
     */
    public function __construct(
        private int    $id,
        private object $coreEntity
    )
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return V
     */
    public function getCoreEntity()
    {
        return $this->coreEntity;
    }
}