<?php

declare(strict_types=1);

use Cache\Adapter\PHPArray\ArrayCachePool;
use Districts\Infrastructure\DoctrinePsrCache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

return function (): EntityManager {
    $metadataConfig = Setup::createAnnotationMetadataConfiguration(
        [__DIR__ . "/src"], // paths
        true, // isDevMode
        null, // proxyDir
        new DoctrinePsrCache(new ArrayCachePool()), // cache
        false // useSimpleAnnotationReader - will be removed in version 3.0
    );
    $connectionConfig = ["url" => getenv("DB_URL")];
    $entityManager = EntityManager::create($connectionConfig, $metadataConfig);
    return $entityManager;
};
