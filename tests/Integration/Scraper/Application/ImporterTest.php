<?php

declare(strict_types=1);

namespace Districts\Test\Integration\Scraper\Application;

use Districts\Core\Infrastructure\Doctrine\CityRepository;
use Districts\Scraper\Application\Importer;
use Districts\Scraper\Domain\CityDTO;
use Districts\Scraper\Domain\DistrictDTO;
use Districts\Test\Integration\DoctrineDbTestCase;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Importer::class)]
final class ImporterTest extends DoctrineDbTestCase
{
    private const string TESTCASE_SQL = <<<'SQL'
BEGIN;
INSERT INTO cities (id, name) VALUES (1, 'Bar');
INSERT INTO districts (city_id, name, area, population) VALUES (1, 'Plugh', 10.0, 5000);
COMMIT;
SQL;

    private Importer $importer;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->loadSql(self::TESTCASE_SQL);
        $this->importer = new Importer(new CityRepository($this->entityManager));
    }

    public function testUpdateExistingDistrict(): void
    {
        $this->importer->import(new CityDTO("Bar", [new DistrictDTO("Plugh", 1, 2)]));

        $this->assertDbTableContents(
            "cities",
            [
                [
                    "name" => "Bar",
                ],
            ]
        );
        $this->assertDbTableContents(
            "districts",
            [
                [
                    "name" => "Plugh",
                    "area" => 1.0,
                    "population" => 2,
                    "city_id" => 1,
                ],
            ]
        );
    }

    public function testSetDistrictsForCityName(): void
    {
        $this->importer->import(new CityDTO("Bar", [new DistrictDTO("Hola", 1, 2)]));

        $this->assertDbTableContents(
            "cities",
            [
                [
                    "name" => "Bar",
                ],
            ]
        );
        $this->assertDbTableContents(
            "districts",
            [
                [
                    "name" => "Hola",
                    "area" => 1.0,
                    "population" => 2,
                    "city_id" => 1,
                ],
            ]
        );
    }

    public function testSetEmptyDistrictsForCityName(): void
    {
        $this->importer->import(new CityDTO("Bar", []));

        $this->assertDbTableContents(
            "cities",
            [
                [
                    "name" => "Bar",
                ],
            ]
        );
        $this->assertDbTableContents(
            "districts",
            []
        );
    }

    public function testSetDistrictsForNonexistentCityName(): void
    {
        $this->importer->import(new CityDTO("New City", [new DistrictDTO("Hola", 1, 2)]));

        $this->assertDbTableContents(
            "cities",
            [
                [
                    "name" => "Bar",
                ],
                [
                    "name" => "New City",
                ],
            ]
        );
        $this->assertDbTableContents(
            "districts",
            [
                [
                    "name" => "Plugh",
                    "area" => 10.0,
                    "population" => 5000,
                    "city_id" => 1,
                ],
                [
                    "name" => "Hola",
                    "area" => 1.0,
                    "population" => 2,
                    "city_id" => 2,
                ],
            ]
        );
    }
}
