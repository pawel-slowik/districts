<?php

// this file is required for vendor/bin/doctrine

declare(strict_types=1);

require_once __DIR__ . "/vendor/autoload.php";

$entityManagerFactory = require "doctrine-bootstrap.php";
$entityManager = $entityManagerFactory();

return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);
