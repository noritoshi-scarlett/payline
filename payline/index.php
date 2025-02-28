<?php
declare(strict_types=1);

use Noritoshi\Payline\Example\Payment\Application\Controller\Controller;

$container = require __DIR__ . '/config/autoload.php';


/** @var Controller $controller */
$controller = $container->get(Controller::class);
$controller->addNewLogForSingleOrder();