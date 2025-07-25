<?php

declare(strict_types=1);

namespace Districts\Test\Integration\Editor\Infrastructure\Doctrine;

use Districts\Core\Domain\Area;
use Districts\Core\Domain\Name;
use Districts\Core\Domain\Population;
use Districts\Core\Infrastructure\NotFoundInRepositoryException;
use Districts\Editor\Infrastructure\Doctrine\DistrictRepository;
use Districts\Test\Integration\DoctrineDbTestCase;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(DistrictRepository::class)]
final class DistrictRepositoryTest extends DoctrineDbTestCase
{
    private const string TESTCASE_SQL = <<<'SQL'
BEGIN;
INSERT INTO cities (id, name) VALUES (1, 'Foo');
INSERT INTO districts (city_id, name, area, population) VALUES (1, 'Plugh', 10.0, 5000);
COMMIT;
SQL;

    private DistrictRepository $districtRepository;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->loadSql(self::TESTCASE_SQL);
        $this->districtRepository = new DistrictRepository($this->entityManager);
    }

    public function testGet(): void
    {
        $district = $this->districtRepository->get(1);
        $this->assertObjectEquals(new Name("Plugh"), $district->getName());
        $this->assertObjectEquals(new Area(10), $district->getArea());
        $this->assertObjectEquals(new Population(5000), $district->getPopulation());
        $this->assertSame("Foo", $district->getCity()->getName());
    }

    public function testGetNonExistent(): void
    {
        $this->expectException(NotFoundInRepositoryException::class);
        $this->districtRepository->get(999);
    }
}
