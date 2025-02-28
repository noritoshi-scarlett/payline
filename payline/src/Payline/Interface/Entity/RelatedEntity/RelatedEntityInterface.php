<?php
declare(strict_types=1);

namespace Noritoshi\Payline\Interface\Entity\RelatedEntity;

use Noritoshi\Payline\Infrastructure\Domain\BasicEntityInterface;

/**
 * @template V of object
 */
interface RelatedEntityInterface extends BasicEntityInterface
{
    /**
     * @return V
     */
    public function getCoreEntity();
}
