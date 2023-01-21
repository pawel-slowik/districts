<?php

declare(strict_types=1);

use Cache\Adapter\PHPArray\ArrayCachePool;
use Districts\Editor\Infrastructure\DoctrinePsrCache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Doctrine\ORM\Tools\Setup;

return function (): EntityManager {
    $metadataConfig = Setup::createConfiguration(
        true, // isDevMode
        null, // proxyDir
        new DoctrinePsrCache(new ArrayCachePool()), // cache
    );
    $metadataConfig->setMetadataDriverImpl(new AttributeDriver([__DIR__ . "/src"]));
    $connectionConfig = ["url" => getenv("DB_URL")];
    $entityManager = EntityManager::create($connectionConfig, $metadataConfig);
    return $entityManager;
};
