<?php

declare(strict_types=1);

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

return function (): EntityManager {
    $metadataConfig = Setup::createAnnotationMetadataConfiguration(
        [__DIR__ . "/src"], // paths
        true, // isDevMode
        null, // proxyDir
        null, // cache
        false // useSimpleAnnotationReader - will be removed in version 3.0
    );
    $connectionConfig = ["url" => getenv("DB_URL")];
    $entityManager = EntityManager::create($connectionConfig, $metadataConfig);
    return $entityManager;
};
