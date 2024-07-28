<?php

declare(strict_types=1);

namespace Districts\Test\Integration\Scraper\Application;

use Districts\Editor\Infrastructure\DoctrineCityRepository;
use Districts\Scraper\Application\Importer;
use Districts\Scraper\Application\ProgressReporter;
use Districts\Scraper\Domain\CityDTO;
use Districts\Scraper\Domain\DistrictDTO;
use Districts\Test\Integration\DoctrineDbTestCase;

/**
 * @covers \Districts\Scraper\Application\Importer
 */
class ImporterTest extends DoctrineDbTestCase
{
    private const TESTCASE_SQL = <<<'SQL'
BEGIN;
INSERT INTO cities (id, name) VALUES (1, 'Bar');
INSERT INTO districts (city_id, name, area, population) VALUES (1, 'Plugh', 10.0, 5000);
COMMIT;
SQL;

    private Importer $importer;

    protected function setUp(): void
    {
        parent::setUp();
        parent::loadSql(self::TESTCASE_SQL);
        $this->importer = new Importer(new DoctrineCityRepository($this->entityManager));
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

    public function testProgressReport(): void
    {
        $progressReporter = $this->createMock(ProgressReporter::class);
        $progressReporter
            ->expects($this->exactly(2))
            ->method("advance");

        $this->importer->import(
            new CityDTO(
                "Foo",
                [
                    new DistrictDTO("Bar", 1, 2),
                    new DistrictDTO("Baz", 3, 4),
                ]
            ),
            $progressReporter
        );
    }
}
