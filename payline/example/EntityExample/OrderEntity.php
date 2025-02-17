<?php
declare(strict_types=1);

namespace Payline\Example\EntityExample;

use Payline\App\Interface\Entity\RelatedEntity\RelatedEntityInterface;

/**
 * @template V of object
 */
class OrderEntity implements RelatedEntityInterface
{
    /**
     * @param V $coreEntity
     */
    public function __construct(
        private readonly int $id,
        private readonly object $coreEntity
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