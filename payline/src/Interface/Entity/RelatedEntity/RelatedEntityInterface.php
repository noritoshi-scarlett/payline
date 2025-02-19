<?php
declare(strict_types=1);

namespace Payline\App\Interface\Entity\RelatedEntity;

use Payline\App\Infrastructure\Domain\BasicEntityInterface;

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
