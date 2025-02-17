<?php
declare(strict_types=1);

include __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/autoload_functions.php';
require __DIR__ . '/database.php';

use Symfony\Component\DependencyInjection\ContainerBuilder;

$container = new ContainerBuilder();

$container
    ->register(PDO::class, PDO::class)
    ->setFactory([PdoFactory::class, 'create'])
    ->setPublic(true);

$interfaceToImplementationMap = [
    Payline\App\Interface\Repository\LogRepositoryInterface::class => Payline\Example\DatabaseImplementationExample\LogRepository::class,
    Payline\App\Application\Factory\LogAbstractFactory::class => Payline\Example\ApplicationUseExample\PaymentLogFactory::class,
    Payline\App\Interface\Entity\Source\SourceInterface::class => Payline\Example\EntityExample\Source::class

];

$argumentMapping = [
    Payline\Example\DatabaseImplementationExample\LogRepository::class => [
        '$tableName' => 'payment_log',
    ],
    Payline\Example\DatabaseImplementationExample\SourceRepository::class => [
        '$tableName' => 'source',
    ],
];

autoloading($container, $interfaceToImplementationMap, $argumentMapping,'Payline\\App\\', realpath(__DIR__ . '/../src/'));
autoloading($container, $interfaceToImplementationMap, $argumentMapping, 'Payline\\Example\\', realpath(__DIR__ . '/../example/'));
autoloading($container, $interfaceToImplementationMap, $argumentMapping, 'Payline\\Test\\', realpath(__DIR__ . '/../test/'));

$container->compile();

return $container;