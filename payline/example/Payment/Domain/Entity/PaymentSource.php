<?php
declare(strict_types=1);

namespace Noritoshi\Payline\Example\Payment\Domain\Entity;

use Noritoshi\Payline\Application\Exception\Validation\InvalidLogStateEnumException;
use Noritoshi\Payline\Interface\Entity\LogEntity\LogEntityInterface;
use Noritoshi\Payline\Interface\Entity\LogEntity\StateEnum\StateEnumGraphCheck;
use Noritoshi\Payline\Interface\Entity\LogEntity\StateEnum\StateEnumInterface;
use Noritoshi\Payline\Interface\Entity\Source\SourceInterface;

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