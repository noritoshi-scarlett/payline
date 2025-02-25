<?php
declare(strict_types=1);

include __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/autoload_functions.php';
require __DIR__ . '/database.php';

use Payline\Example\Domain\Repository\LogRepository;
use Payline\Example\Domain\Repository\SourceRepository;
use Payline\Example\Payment\Application\Factory\PaymentLogFactory;
use Payline\Example\Payment\Domain\Entity\PaymentSource;
use Symfony\Component\DependencyInjection\ContainerBuilder;

$container = new ContainerBuilder();

$container
    ->register(PDO::class, PDO::class)
    ->setFactory([PdoFactory::class, 'create'])
    ->setPublic(true);

$interfaceToImplementationMap = [
    Payline\App\Interface\Repository\LogRepositoryInterface::class => LogRepository::class,
    Payline\App\Application\Factory\LogAbstractFactory::class => PaymentLogFactory::class,
    Payline\App\Interface\Entity\Source\SourceInterface::class => PaymentSource::class

];

$argumentMapping = [
    LogRepository::class => [
        '$tableName' => 'payment_log',
    ],
    SourceRepository::class => [
        '$tableName' => 'source',
    ],
];

autoloading($container, $interfaceToImplementationMap, $argumentMapping,'Payline\\App\\', realpath(__DIR__ . '/../src/'));
autoloading($container, $interfaceToImplementationMap, $argumentMapping, 'Payline\\Example\\', realpath(__DIR__ . '/../example/'));
autoloading($container, $interfaceToImplementationMap, $argumentMapping, 'Payline\\Test\\', realpath(__DIR__ . '/../test/'));

$container->compile();

return $container;