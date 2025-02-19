<?php
declare(strict_types=1);

namespace Payline\App\Interface\Entity\LogEntity\StateEnum;

use Payline\App\Infrastructure\Domain\BasicEnumInterface;

/**
 * @use StateEnumGraphCheck for check
 */
interface StateEnumInterface extends BasicEnumInterface
{
    public function getInitializeStates(): array;
    public function getFinalStates(): array;
    public function getGraph(): array;
}