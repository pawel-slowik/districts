<?php

declare(strict_types=1);

namespace Districts\Test\Infrastructure;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;

class FixtureTool
{
    public static function reset(EntityManager $entityManager): void
    {
        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->dropDatabase();
        $schemaTool->createSchema($entityManager->getMetadataFactory()->getAllMetadata());
    }

    public static function loadFiles(EntityManager $entityManager, iterable $sqlFilenames): void
    {
        foreach ($sqlFilenames as $sqlFilename) {
            $entityManager->getConnection()->exec(file_get_contents($sqlFilename));
        }
    }
}
