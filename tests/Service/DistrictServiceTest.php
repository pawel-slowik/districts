<?php

declare(strict_types=1);

namespace Test\Service;

use Application\Command\AddDistrictCommand;
use Application\Command\UpdateDistrictCommand;

use DomainModel\Entity\District;
use DomainModel\DistrictFilter;
use DomainModel\DistrictOrdering;

use Service\DistrictService;
use Service\CityIterator;
use Service\NotFoundException;
use Service\ValidationException;
use Validator\DistrictValidator;

use Repository\CityRepository;
use Repository\DistrictRepository;

use Test\Repository\FixtureTool;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Service\DistrictService
 */
class DistrictServiceTest extends TestCase
{
    /**
     * @var DistrictService
     */
    private $districtService;

    /**
     * @var DistrictOrdering
     */
    private $defaultOrder;

    protected function setUp(): void
    {
        $entityManager = (require "doctrine-bootstrap.php")();
        FixtureTool::reset($entityManager);
        FixtureTool::load($entityManager, [
            "tests/Repository/data/cities.sql",
            "tests/Repository/data/districts.sql",
        ]);
        $cityRepository = new CityRepository($entityManager);
        $this->districtService = new DistrictService(
            new DistrictRepository($entityManager),
            new DistrictValidator(
                new CityIterator($cityRepository),
            ),
            $cityRepository
        );
        $this->defaultOrder = new DistrictOrdering(DistrictOrdering::FULL_NAME, DistrictOrdering::ASC);
    }

    public function testGet(): void
    {
        $district = $this->districtService->get("1");
        $this->assertInstanceOf(District::class, $district);
        $this->assertSame("Plugh", $district->getName());
        $this->assertSame(floatval(10), $district->getArea());
        $this->assertSame(5000, $district->getPopulation());
        $this->assertSame("Foo", $district->getCity()->getName());
    }

    public function testGetNonExistent(): void
    {
        $this->expectException(NotFoundException::class);
        $this->districtService->get("999");
    }

    public function testRemove(): void
    {
        $this->districtService->remove("1");
        $this->assertCount(
            14,
            $this->districtService->list($this->defaultOrder, null)
        );
        $this->expectException(NotFoundException::class);
        $this->districtService->get("1");
    }

    public function testRemoveNonExistent(): void
    {
        $this->expectException(NotFoundException::class);
        $this->districtService->remove("999");
    }

    public function testAdd(): void
    {
        $this->districtService->add(new AddDistrictCommand(1, "Lorem ipsum", 12.3, 456));
        $this->assertCount(
            16,
            $this->districtService->list(
                $this->defaultOrder,
                null,
            )
        );
        $this->assertNotEmpty(
            $this->districtService->list(
                $this->defaultOrder,
                new DistrictFilter(DistrictFilter::TYPE_NAME, "Lorem ipsum"),
            )
        );
    }

    /**
     * @dataProvider addInvalidDataProvider
     */
    public function testAddInvalid(AddDistrictCommand $command): void
    {
        $this->expectException(ValidationException::class);
        $this->districtService->add($command);
    }

    public function addInvalidDataProvider(): array
    {
        return [
            "area_less_than_zero" => [
                new AddDistrictCommand(
                    1,
                    "test",
                    -1,
                    2,
                ),
            ],
            "population_less_than_zero" => [
                new AddDistrictCommand(
                    1,
                    "test",
                    1,
                    -1,
                ),
            ],
            "nonexistent_city_id" => [
                new AddDistrictCommand(
                    999,
                    "test",
                    1,
                    1,
                ),
            ],
        ];
    }

    public function testUpdate(): void
    {
        $this->districtService->update(new UpdateDistrictCommand(1, "update test", 111.22, 333));
        $updatedDistrict = $this->districtService->get("1");
        $this->assertSame("update test", $updatedDistrict->getName());
        $this->assertSame(111.22, $updatedDistrict->getArea());
        $this->assertSame(333, $updatedDistrict->getPopulation());
    }

    /**
     * @dataProvider updateInvalidDataProvider
     */
    public function testUpdateInvalid(UpdateDistrictCommand $command): void
    {
        $this->expectException(ValidationException::class);
        $this->districtService->update($command);
    }

    public function updateInvalidDataProvider(): array
    {
        return [
            "area_less_than_zero" => [
                new UpdateDistrictCommand(
                    1,
                    "test",
                    -1,
                    2,
                ),
            ],
            "population_less_than_zero" => [
                new UpdateDistrictCommand(
                    1,
                    "test",
                    1,
                    -1,
                ),
            ],
        ];
    }

    public function testUpdateNonexistent(): void
    {
        $this->expectException(NotFoundException::class);
        $this->districtService->update(new UpdateDistrictCommand(999, "test", 1, 2));
    }
}
