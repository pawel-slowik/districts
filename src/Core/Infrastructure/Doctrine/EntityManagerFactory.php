<?php

declare(strict_types=1);

namespace Districts\Core\Infrastructure\Doctrine;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Doctrine\ORM\ORMSetup;

final class EntityManagerFactory
{
    public static function create(string $entitySourcePath): EntityManager
    {
        $metadataConfig = ORMSetup::createConfiguration(true);
        $metadataConfig->setMetadataDriverImpl(new AttributeDriver([$entitySourcePath]));
        $driverMap = [
            'sqlite' => 'pdo_sqlite',
            'mysql' => 'pdo_mysql',
        ];
        $entityManager = new EntityManager(
            DriverManager::getConnection((new DsnParser($driverMap))->parse((string) getenv('DB_URL'))),
            $metadataConfig,
        );
        return $entityManager;
    }
}
