<?php
declare(strict_types=1);

namespace Payline\App\Interface\Entity\LogEntity\StateEnum;

use Payline\Example\EntityExample\PaymentLogEnum;
use Payline\App\Application\Exception\InvalidLogStateEnumException;

class StateEnumGraphCheck
{
    /**
     * @param StateEnumInterface $currentState
     * @return array<PaymentLogEnum>
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
