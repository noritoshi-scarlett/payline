<?php
declare(strict_types=1);

namespace Payline\App\Application\Utility\Validator;

use Payline\App\Application\Exception\Validation\InvalidDateException;
use Payline\App\Application\Exception\Validation\InvalidLogStateEnumException;
use Payline\App\Interface\Entity\LogEntity\LogEntityInterface;
use Payline\App\Interface\Entity\LogEntity\StateEnum\StateEnumInterface;
use Payline\App\Interface\Entity\Source\SourceInterface;

class LogValidator
{
    /**
     * @throws InvalidLogStateEnumException
     * @throws InvalidDateException
     */
    public static function newLogDataCompareToNewestLog(
        LogEntityInterface $newestLog,
        SourceInterface $source,
        StateEnumInterface $state,
        \DateTimeImmutable $createdAt
    ): true
    {
        $stateAllowed = $source->isStateAllowedForNextLog($newestLog, $state);
        $dateAllowed = (float) $createdAt->format('U.u') >= (float) $newestLog->getCreatedAt()->format('U.u');

        if (!$stateAllowed) {
            throw new InvalidLogStateEnumException('State is not allowed.')->states($state, $newestLog->getState());
        }
        if (!$dateAllowed) {
            throw new InvalidDateException('Date is too old.')->IsOlderDate($createdAt, $newestLog->getCreatedAt());
        }

        return true;
    }
}
