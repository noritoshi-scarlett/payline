<?php
declare(strict_types=1);

use Payline\Example\ApplicationUseExample\Controller\Controller;

$container = require __DIR__ . '/config/autoload.php';


/** @var Controller $controller */
$controller = $container->get(Controller::class);
$controller->addNewLogForSingleOrder();