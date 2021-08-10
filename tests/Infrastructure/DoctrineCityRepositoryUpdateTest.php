<?php

declare(strict_types=1);

namespace Districts\Test\Infrastructure;

use Districts\DomainModel\Entity\City;
use Districts\DomainModel\VO\Area;
use Districts\DomainModel\VO\Name;
use Districts\DomainModel\VO\Population;
use Districts\Infrastructure\DoctrineCityRepository;

/**
 * @covers \Districts\Infrastructure\DoctrineCityRepository
 */
class DoctrineCityRepositoryUpdateTest extends DoctrineDbTestCase
{
    private const TESTCASE_SQL = <<<'SQL'
BEGIN;
INSERT INTO cities (id, name) VALUES (1, 'Foo');
INSERT INTO districts (city_id, name, area, population) VALUES (1, 'Plugh', 10.0, 5000);
COMMIT;
SQL;

    /**
     * @var DoctrineCityRepository
     */
    private $cityRepository;

    protected function setUp(): void
    {
        parent::setUp();
        parent::loadSql(self::TESTCASE_SQL);

        $this->cityRepository = new DoctrineCityRepository($this->entityManager);
    }

    public function testAdd(): void
    {
        $this->cityRepository->add(new City("Baz"));

        $this->assertDbTableContents(
            "cities",
            [
                ["name" => "Foo"],
                ["name" => "Baz"],
            ],
        );
    }

    public function testUpdateWithNewDistrict(): void
    {
        $city = $this->cityRepository->get(1);
        $city->addDistrict(new Name("New District"), new Area(123.4), new Population(5678));
        $this->cityRepository->update($city);

        $this->assertDbTableContents(
            "districts",
            [
                [
                    "city_id" => "1",
                    "name" => "Plugh",
                    "area" => "10.0",
                    "population" => "5000",
                ],
                [
                    "city_id" => "1",
                    "name" => "New District",
                    "area" => "123.4",
                    "population" => "5678",
                ],
            ],
        );
    }

    public function testUpdateWithChangedDistrict(): void
    {
        $city = $this->cityRepository->get(1);
        $city->updateDistrict(1, new Name("Updated District"), new Area(123.4), new Population(5678));
        $this->cityRepository->update($city);

        $this->assertDbTableContents(
            "districts",
            [
                [
                    "city_id" => "1",
                    "name" => "Updated District",
                    "area" => "123.4",
                    "population" => "5678",
                ],
            ],
        );
    }
}
