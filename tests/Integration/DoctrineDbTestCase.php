<?php

declare(strict_types=1);

namespace Districts\Test\Integration;

use Districts\Core\Infrastructure\DoctrineEntityManagerFactory;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

abstract class DoctrineDbTestCase extends TestCase
{
    protected EntityManager $entityManager;

    protected function setUp(): void
    {
        $this->entityManager = DoctrineEntityManagerFactory::create(__DIR__ . '/../../src');
        FixtureTool::reset($this->entityManager);
    }

    protected function loadSql(string $sql): void
    {
        FixtureTool::loadSql($this->entityManager, $sql);
    }

    protected function loadDefaultDbContents(): void
    {
        FixtureTool::loadFiles(
            $this->entityManager,
            [
                "tests/Integration/Editor/Infrastructure/data/cities.sql",
                "tests/Integration/Editor/Infrastructure/data/districts.sql",
            ]
        );
    }

    /**
     * @param array<array<string, mixed>> $expectedContents
     */
    protected function assertDbTableContents(string $tableName, array $expectedContents): void
    {
        $result = $this->entityManager->getConnection()->fetchAllAssociative("SELECT * FROM {$tableName}");

        $this->assertSame(
            $this->sortTableContentsForComparision($expectedContents),
            $this->sortTableContentsForComparision($this->removeIdsFromTableContents($result))
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

    /**
     * @param array<array<string, mixed>> $tableContents
     *
     * @return array<array<string, mixed>>
     */
    private function sortTableContentsForComparision(array $tableContents): array
    {
        foreach (array_keys($tableContents) as $offset) {
            ksort($tableContents[$offset]);
        }

        usort(
            $tableContents,
            static fn (array $a, array $b): int => strcmp(serialize($a), serialize($b))
        );

        return $tableContents;
    }
}
