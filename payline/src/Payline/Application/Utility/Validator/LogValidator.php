<?php
declare(strict_types=1);

namespace Noritoshi\Payline\Application\Utility\Validator;

use Noritoshi\Payline\Application\Exception\Validation\InvalidDateException;
use Noritoshi\Payline\Application\Exception\Validation\InvalidLogStateEnumException;
use Noritoshi\Payline\Interface\Entity\LogEntity\LogEntityInterface;
use Noritoshi\Payline\Interface\Entity\LogEntity\StateEnum\StateEnumInterface;
use Noritoshi\Payline\Interface\Entity\Source\SourceInterface;

class LogValidator
{
    /**
     * @throws InvalidLogStateEnumException
     * @throws InvalidDateException
     */
    public static function newLogDataCompareToLatestLog(
        ?LogEntityInterface $latestLog,
        SourceInterface $source,
        StateEnumInterface $state,
        \DateTimeImmutable $createdAt
    ): true
    {
        $stateAllowed = $source->isStateAllowedForNextLog($latestLog, $state);
        $dateAllowed = !isset($latestLog) || (float) $createdAt->format('U.u') >= (float) $latestLog->getCreatedAt()->format('U.u');

        if (!$stateAllowed) {
            throw new InvalidLogStateEnumException('State is not allowed.')->states($state, $latestLog?->getState());
        }
        if (!$dateAllowed) {
            throw new InvalidDateException('Date is too old.')->IsOlderDate($createdAt, $latestLog->getCreatedAt());
        }

        return true;
    }
}
