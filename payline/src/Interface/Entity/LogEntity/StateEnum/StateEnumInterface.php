<?php
declare(strict_types=1);

namespace Payline\App\Interface\Entity\LogEntity\StateEnum;

/**
 * @use StateEnumGraphCheck for check
 */
interface StateEnumInterface
{
    public function getInitializeStates(): array;
    public function getFinalStates(): array;
    public function getGraph(): array;
}