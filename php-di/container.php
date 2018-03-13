<?php
// Called in /bootstrap.php

use DI\ContainerBuilder;

// Instantiation of DI Container for the main.php game
$builder = new ContainerBuilder();
$builder->addDefinitions('./php-di/config.php');

$container = $builder->build();