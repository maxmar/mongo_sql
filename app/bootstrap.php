<?php

use Core\DIContainer;
use DI\ContainerBuilder;

require __DIR__ . '/../vendor/autoload.php';

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/config/services.php');
$container = $containerBuilder->build();

return $container;