<?php

namespace Noritoshi\Payline\Test\TestDataProviders;

use Noritoshi\Payline\Interface\Entity\LogEntity\StateEnum\StateEnumInterface;

enum SampleStateEnum:string implements StateEnumInterface
{
    case INITIALIZED = 'initialized';
    case FINALIZED = 'finalized';
    case PROCESSING = 'processing';

    public function getInitializeStates(): array
    {
        return [self::INITIALIZED];
    }

    public function getFinalStates(): array
    {
        return [self::FINALIZED];
    }

    public function getGraph(): array
    {
        return [
            self::INITIALIZED->name => [self::PROCESSING, self::FINALIZED],
            self::PROCESSING->name => [self::FINALIZED],
            self::FINALIZED->name => []
        ];
    }
}
