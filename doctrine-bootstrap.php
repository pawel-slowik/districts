<?php

declare(strict_types=1);

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Doctrine\ORM\ORMSetup;

return function (): EntityManager {
    $metadataConfig = ORMSetup::createConfiguration(true);
    $metadataConfig->setMetadataDriverImpl(new AttributeDriver([__DIR__ . "/src"]));
    $connectionConfig = ["url" => getenv("DB_URL")];
    $entityManager = EntityManager::create($connectionConfig, $metadataConfig);
    return $entityManager;
};
