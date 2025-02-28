<?php
declare(strict_types=1);

namespace Noritoshi\Payline\Interface\Entity\LogEntity\StateEnum;

use Noritoshi\Payline\Application\Exception\Validation\InvalidLogStateEnumException;
use Noritoshi\Payline\Example\Payment\Plugin\PayU\Domain\Entity\PayUPaymentLogEnum;

class StateEnumGraphCheck
{
    /**
     * @param StateEnumInterface $currentState
     * @return array<PayUPaymentLogEnum>
     * @throws InvalidLogStateEnumException
     */
    public static function getStatesPossibleToTransitionFromCurrent(StateEnumInterface $currentState): array
    {
        if ($currentState::tryFrom($currentState->value) === null) {
            throw new InvalidLogStateEnumException('Invalid state.');
        }

        return $currentState->getGraph()[$currentState->name]
            ?? throw new InvalidLogStateEnumException('State not found in graph.');
    }
}
