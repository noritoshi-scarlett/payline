<?php
declare(strict_types=1);

include __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/autoload_functions.php';
require __DIR__ . '/database.php';
require __DIR__ . '/redis.php';

use Noritoshi\Payline\Application\Factory\LogAbstractFactory;
use Noritoshi\Payline\Example\Payment\Application\Factory\PaymentLogFactory;
use Noritoshi\Payline\Example\Payment\Domain\Entity\PaymentSource;
use Noritoshi\Payline\Example\Payment\Domain\Repository\PaymentLogRepository;
use Noritoshi\Payline\Example\Payment\Domain\Repository\PaymentSourceRepository;
use Noritoshi\Payline\Infrastructure\Library\Cache\CacheSystemInterface;
use Noritoshi\Payline\Infrastructure\Library\Cache\RedisCacheSystem;
use Noritoshi\Payline\Interface\Entity\Source\SourceInterface;
use Noritoshi\Payline\Interface\Repository\LogRepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

$container = new ContainerBuilder();

$container
    ->register(PDO::class, PDO::class)
    ->setFactory([PdoFactory::class, 'create'])
    ->setPublic(true);

$container
    ->register(RedisCacheSystem::class, RedisCacheSystem::class)
    ->setFactory([RedisFactory::class, 'create'])
    ->setPublic(true);

$interfaceToImplementationMap = [
    LogRepositoryInterface::class => PaymentLogRepository::class,
    LogAbstractFactory::class => PaymentLogFactory::class,
    SourceInterface::class => PaymentSource::class,
    CacheSystemInterface::class => RedisCacheSystem::class,
];

$classesToSkip = [RedisCacheSystem::class];

$argumentMapping = [
    PaymentLogRepository::class => [
        '$tableName' => 'payment_log',
    ],
    PaymentSourceRepository::class => [
        '$tableName' => 'source',
    ],
];

autoloading($container, $interfaceToImplementationMap, $classesToSkip, $argumentMapping,'Noritoshi\\Payline\\', realpath(__DIR__ . '/../src/Payline'));
autoloading($container, $interfaceToImplementationMap, $classesToSkip, $argumentMapping, 'Noritoshi\\Payline\\Example\\', realpath(__DIR__ . '/../example/'));
autoloading($container, $interfaceToImplementationMap, $classesToSkip, $argumentMapping, 'Noritoshi\\Payline\\Test\\', realpath(__DIR__ . '/../test/'));

$container->compile();

return $container;