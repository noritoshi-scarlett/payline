<?php
declare(strict_types=1);

namespace Payline\App\Interface\Entity\LogEntity\StateEnum;

use BackedEnum;
use Payline\Example\EntityExample\PaymentLogEnum;
use Payline\App\Application\Exception\InvalidLogStateEnumException;

class StateEnumGraphCheck
{
    /**
     * @param StateEnumInterface&BackedEnum $currentState
     * @return array<PaymentLogEnum>
     * @throws InvalidLogStateEnumException
     */
    public static function getStatesPossibleToTransitionFromCurrent(StateEnumInterface&BackedEnum $currentState): array
    {
        /** @var BackedEnum $currentState */
        if ($currentState::tryFrom($currentState->value) === null) {
            throw new InvalidLogStateEnumException('Invalid state.');
        }

        return $currentState->getGraph()[$currentState->name]
            ?? throw new InvalidLogStateEnumException('State not found in graph.');
    }
}
