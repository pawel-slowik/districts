<?php

declare(strict_types=1);

namespace Districts\Test\Integration\Core\Infrastructure\Doctrine;

use Districts\Core\Domain\Area;
use Districts\Core\Domain\City;
use Districts\Core\Domain\Name;
use Districts\Core\Domain\Population;
use Districts\Core\Infrastructure\Doctrine\CityRepository;
use Districts\Test\Integration\DoctrineDbTestCase;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CityRepository::class)]
final class CityRepositoryUpdateTest extends DoctrineDbTestCase
{
    private const string TESTCASE_SQL = <<<'SQL'
BEGIN;
INSERT INTO cities (id, name) VALUES (1, 'Foo');
INSERT INTO districts (city_id, name, area, population) VALUES (1, 'Plugh', 10.0, 5000);
COMMIT;
SQL;

    private CityRepository $cityRepository;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->loadSql(self::TESTCASE_SQL);

        $this->cityRepository = new CityRepository($this->entityManager);
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
                    "city_id" => 1,
                    "name" => "Plugh",
                    "area" => 10.0,
                    "population" => 5000,
                ],
                [
                    "city_id" => 1,
                    "name" => "New District",
                    "area" => 123.4,
                    "population" => 5678,
                ],
            ],
        );
    }

    public function testUpdateWithChangedDistrict(): void
    {
        $city = $this->cityRepository->get(1);
        $city->updateDistrict(new Name("Plugh"), new Name("Updated District"), new Area(123.4), new Population(5678));
        $this->cityRepository->update($city);

        $this->assertDbTableContents(
            "districts",
            [
                [
                    "city_id" => 1,
                    "name" => "Updated District",
                    "area" => 123.4,
                    "population" => 5678,
                ],
            ],
        );
    }
}
