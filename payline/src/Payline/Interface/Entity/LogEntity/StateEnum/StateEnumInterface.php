<?php
declare(strict_types=1);

namespace Noritoshi\Payline\Interface\Entity\LogEntity\StateEnum;

use Noritoshi\Payline\Infrastructure\Domain\BasicEnumInterface;

/**
 * @use StateEnumGraphCheck for check
 */
interface StateEnumInterface extends BasicEnumInterface
{
    public function getInitializeStates(): array;
    public function getFinalStates(): array;
    public function getGraph(): array;
}