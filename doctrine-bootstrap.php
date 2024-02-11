<?php

declare(strict_types=1);

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Doctrine\ORM\ORMSetup;

return function (): EntityManager {
    $metadataConfig = ORMSetup::createConfiguration(true);
    $metadataConfig->setMetadataDriverImpl(new AttributeDriver([__DIR__ . "/src"]));
    $driverMap = [
        'sqlite' => 'pdo_sqlite',
        'mysql' => 'pdo_mysql',
    ];
    $entityManager = new EntityManager(
        DriverManager::getConnection((new DsnParser($driverMap))->parse(getenv('DB_URL'))),
        $metadataConfig,
    );
    return $entityManager;
};
