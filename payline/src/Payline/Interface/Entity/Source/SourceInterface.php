<?php
declare(strict_types=1);

namespace Noritoshi\Payline\Interface\Entity\Source;

use Noritoshi\Payline\Infrastructure\Domain\BasicEntityInterface;
use Noritoshi\Payline\Interface\Entity\LogEntity\LogEntityInterface;
use Noritoshi\Payline\Interface\Entity\LogEntity\StateEnum\StateEnumInterface;

interface SourceInterface extends BasicEntityInterface
{
    public function getName(): string;

    public function isStateAllowedForNextLog(?LogEntityInterface $log, StateEnumInterface $state): bool;
}