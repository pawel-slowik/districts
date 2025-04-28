<?php

declare(strict_types=1);

namespace Districts\Test\Integration;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use LogicException;

final class FixtureTool
{
    public static function reset(EntityManager $entityManager): void
    {
        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->dropDatabase();
        $schemaTool->createSchema($entityManager->getMetadataFactory()->getAllMetadata());
    }

    /**
     * @param iterable<string> $sqlFilenames
     */
    public static function loadFiles(EntityManager $entityManager, iterable $sqlFilenames): void
    {
        foreach ($sqlFilenames as $sqlFilename) {
            self::loadFile($entityManager, $sqlFilename);
        }
    }

    public static function loadFile(EntityManager $entityManager, string $sqlFilename): void
    {
        $sqlStatements = file_get_contents($sqlFilename);
        if ($sqlStatements === false) {
            throw new LogicException();
        }
        $entityManager->getConnection()->executeStatement($sqlStatements);
    }

    public static function loadSql(EntityManager $entityManager, string $sqlStatement): void
    {
        $entityManager->getConnection()->executeStatement($sqlStatement);
    }
}
