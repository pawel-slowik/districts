<?php

declare(strict_types=1);

namespace Districts\Test\Integration\Editor\Infrastructure;

use Districts\Core\Domain\Area;
use Districts\Core\Domain\Name;
use Districts\Core\Domain\Population;
use Districts\Core\Infrastructure\NotFoundInRepositoryException;
use Districts\Editor\Infrastructure\DistrictFilter\FilterFactory;
use Districts\Editor\Infrastructure\DoctrineDistrictRepository;
use Districts\Test\Integration\DoctrineDbTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(DoctrineDistrictRepository::class)]
class DoctrineDistrictRepositoryTest extends DoctrineDbTestCase
{
    private const TESTCASE_SQL = <<<'SQL'
BEGIN;
INSERT INTO cities (id, name) VALUES (1, 'Foo');
INSERT INTO districts (city_id, name, area, population) VALUES (1, 'Plugh', 10.0, 5000);
COMMIT;
SQL;

    private DoctrineDistrictRepository $districtRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadSql(self::TESTCASE_SQL);
        $this->districtRepository = new DoctrineDistrictRepository(
            $this->entityManager,
            new FilterFactory(),
        );
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
