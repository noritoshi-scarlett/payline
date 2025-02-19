<?php
declare(strict_types=1);

namespace Payline\App\Interface\Entity\Source;

use Payline\App\Infrastructure\Domain\BasicEntityInterface;
use Payline\App\Interface\Entity\LogEntity\LogEntityInterface;
use Payline\App\Interface\Entity\LogEntity\StateEnum\StateEnumInterface;

interface SourceInterface extends BasicEntityInterface
{
    public function getName(): string;

    public function isStateAllowedForNextLog(LogEntityInterface $log, StateEnumInterface $state): bool;
}