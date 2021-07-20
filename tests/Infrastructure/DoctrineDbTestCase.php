<?php

declare(strict_types=1);

namespace Districts\Test\Infrastructure;

use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

abstract class DoctrineDbTestCase extends TestCase
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    protected function setUp(): void
    {
        $this->entityManager = (require "doctrine-bootstrap.php")();
        FixtureTool::reset($this->entityManager);
    }

    protected function loadDefaultDbContents(): void
    {
        FixtureTool::loadFiles(
            $this->entityManager,
            [
                "tests/Infrastructure/data/cities.sql",
                "tests/Infrastructure/data/districts.sql",
            ]
        );
    }

    protected function assertDbTableContents(string $tableName, array $expectedContents): void
    {
        $statement = $this->entityManager->getConnection()->query("SELECT * FROM {$tableName}");
        $result = $statement->fetchAllAssociative();

        $this->assertSame(
            $this->sortTableContentsForComparision($expectedContents),
            $this->sortTableContentsForComparision($this->removeIdsFromTableContents($result))
        );
    }

    private function removeIdsFromTableContents(array $tableContents): array
    {
        return array_map(
            function (array $row): array {
                if (array_key_exists("id", $row)) {
                    unset($row["id"]);
                }
                return $row;
            },
            $tableContents
        );
    }

    private function sortTableContentsForComparision(array $tableContents): array
    {
        usort(
            $tableContents,
            function (array $a, array $b): int {
                return strcmp(serialize($a), serialize($b));
            }
        );

        return $tableContents;
    }
}
