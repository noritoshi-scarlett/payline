<?php
declare(strict_types=1);

namespace Payline\Example\Payment\Domain\Entity;

use Payline\App\Application\Exception\Validation\InvalidLogStateEnumException;
use Payline\App\Interface\Entity\LogEntity\LogEntityInterface;
use Payline\App\Interface\Entity\LogEntity\StateEnum\StateEnumGraphCheck;
use Payline\App\Interface\Entity\LogEntity\StateEnum\StateEnumInterface;
use Payline\App\Interface\Entity\Source\SourceInterface;

class PaymentSource implements SourceInterface
{
    public function __construct(
        private int $id,
        private string $name,
    )
    {
    }


    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @throws InvalidLogStateEnumException
     */
    public function isStateAllowedForNextLog(?LogEntityInterface $log, StateEnumInterface $state): bool
    {
        if (!isset($log)) {
            return in_array($state, $state->getInitializeStates());
        }

        return in_array($state, StateEnumGraphCheck::getStatesPossibleToTransitionFromCurrent($log->getState()));
    }
}