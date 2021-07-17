<?php

// this file is required for vendor/bin/doctrine

declare(strict_types=1);

require_once __DIR__ . "/vendor/autoload.php";

use Doctrine\ORM\Tools\Console\ConsoleRunner;

$entityManagerFactory = require "doctrine-bootstrap.php";
$entityManager = $entityManagerFactory();

return ConsoleRunner::createHelperSet($entityManager);
