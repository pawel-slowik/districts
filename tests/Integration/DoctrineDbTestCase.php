<?php

declare(strict_types=1);

namespace Districts\Test\Integration;

use Districts\Core\Infrastructure\Doctrine\EntityManagerFactory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Events;
use PHPUnit\Framework\TestCase;

abstract class DoctrineDbTestCase extends TestCase
{
    protected EntityManager $entityManager;

    protected function setUp(): void
    {
        $this->entityManager = EntityManagerFactory::create(__DIR__ . '/../../src');
        // tests are running on a SQLite database, which doesn't support the required collation
        $this->entityManager->getEventManager()->addEventListener(
            Events::loadClassMetadata,
            new RemoveCollationListener()
        );
        FixtureTool::reset($this->entityManager);
    }

    protected function loadSql(string $sql): void
    {
        FixtureTool::loadSql($this->entityManager, $sql);
    }

    /**
     * @param string[] $fileNames
     */
    protected function loadFiles(array $fileNames): void
    {
        FixtureTool::loadFiles($this->entityManager, $fileNames);
    }

    /**
     * @param array<array<string, mixed>> $expectedContents
     */
    protected function assertDbTableContents(string $tableName, array $expectedContents): void
    {
        $result = $this->entityManager->getConnection()->fetchAllAssociative("SELECT * FROM {$tableName}");

        $this->assertArraysHaveIdenticalValuesIgnoringOrder(
            $expectedContents,
            $this->removeIdsFromTableContents($result)
        );
    }

    /**
     * @param array<array<string, mixed>> $tableContents
     *
     * @return array<array<string, mixed>>
     */
    private function removeIdsFromTableContents(array $tableContents): array
    {
        return array_map(
            static function (array $row): array {
                if (array_key_exists("id", $row)) {
                    unset($row["id"]);
                }
                return $row;
            },
            $tableContents
        );
    }
}
