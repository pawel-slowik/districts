<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\Application;

use Districts\Editor\Application\Command\AddDistrictCommand;
use Districts\Editor\Application\Command\UpdateDistrictCommand;
use Districts\Editor\Application\DistrictValidator;
use Districts\Editor\Domain\City;
use Districts\Editor\Domain\CityRepository;
use Districts\Editor\Domain\District;
use Districts\Editor\Domain\DistrictRepository;
use Districts\Editor\Domain\Name;
use Districts\Editor\Infrastructure\NotFoundInRepositoryException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

#[CoversClass(DistrictValidator::class)]
class DistrictValidatorTest extends TestCase
{
    private DistrictValidator $districtValidator;

    /** @var CityRepository&Stub */
    private CityRepository $cityRepository;

    /** @var DistrictRepository&Stub */
    private DistrictRepository $districtRepository;

    protected function setUp(): void
    {
        $this->cityRepository = $this->createStub(CityRepository::class);
        $this->districtRepository = $this->createStub(DistrictRepository::class);
        $this->districtValidator = new DistrictValidator($this->cityRepository, $this->districtRepository);
    }

    public function testAddInvalidName(): void
    {
        $result = $this->districtValidator->validateAdd(new AddDistrictCommand(1, "", 1, 1));

        $this->assertFalse($result->isOk());
        $this->assertEqualsCanonicalizing(["name"], $result->getErrors());
    }

    public function testAddDuplicatedName(): void
    {
        $city = $this->createStub(City::class);
        $city
            ->method("hasDistrictWithName")
            ->willReturn(true);
        $this->cityRepository
            ->method("get")
            ->willReturnMap([[1, $city]]);

        $result = $this->districtValidator->validateAdd(new AddDistrictCommand(1, "valid name", 1, 1));

        $this->assertFalse($result->isOk());
        $this->assertEqualsCanonicalizing(["name"], $result->getErrors());
    }

    public function testAddInvalidArea(): void
    {
        $result = $this->districtValidator->validateAdd(new AddDistrictCommand(1, "Foo", 0, 1));

        $this->assertFalse($result->isOk());
        $this->assertEqualsCanonicalizing(["area"], $result->getErrors());
    }

    public function testAddInvalidPopulation(): void
    {
        $result = $this->districtValidator->validateAdd(new AddDistrictCommand(1, "Foo", 1, 0));

        $this->assertFalse($result->isOk());
        $this->assertEqualsCanonicalizing(["population"], $result->getErrors());
    }

    public function testAddNonexistentCityId(): void
    {
        $this->cityRepository
            ->method("get")
            ->will($this->throwException(new NotFoundInRepositoryException()));

        $result = $this->districtValidator->validateAdd(new AddDistrictCommand(1, "Foo", 1, 1));

        $this->assertFalse($result->isOk());
        $this->assertEqualsCanonicalizing(["city"], $result->getErrors());
    }

    public function testAddMultipleErrors(): void
    {
        $result = $this->districtValidator->validateAdd(new AddDistrictCommand(1, "", 0, 0));

        $this->assertFalse($result->isOk());
        $this->assertEqualsCanonicalizing(["name", "area", "population"], $result->getErrors());
    }

    public function testUpdateInvalidName(): void
    {
        $result = $this->districtValidator->validateUpdate(new UpdateDistrictCommand(1, "", 1, 1));

        $this->assertFalse($result->isOk());
        $this->assertEqualsCanonicalizing(["name"], $result->getErrors());
    }

    public function testUpdateDuplicatedName(): void
    {
        $district = $this->createMock(District::class);
        $this->districtRepository
            ->method("get")
            ->willReturnMap([[1, $district]]);
        $city = $this->createStub(City::class);
        $city
            ->method("hasDistrictWithName")
            ->willReturn(true);
        $district
            ->method("getCity")
            ->willReturn($city);

        $result = $this->districtValidator->validateUpdate(new UpdateDistrictCommand(1, "valid name", 1, 1));

        $this->assertFalse($result->isOk());
        $this->assertEqualsCanonicalizing(["name"], $result->getErrors());
    }

    public function testUpdateAllowsUnchangedName(): void
    {
        $district = $this->createMock(District::class);
        $district
            ->method("getName")
            ->willReturn(new Name("unchanged name"));
        $this->districtRepository
            ->method("get")
            ->willReturnMap([[1, $district]]);
        $city = $this->createStub(City::class);
        $city
            ->method("hasDistrictWithName")
            ->willReturn(true);
        $district
            ->method("getCity")
            ->willReturn($city);

        $result = $this->districtValidator->validateUpdate(new UpdateDistrictCommand(1, "unchanged name", 1, 1));

        $this->assertTrue($result->isOk());
    }

    public function testUpdateInvalidArea(): void
    {
        $result = $this->districtValidator->validateUpdate(new UpdateDistrictCommand(1, "Foo", 0, 1));

        $this->assertFalse($result->isOk());
        $this->assertEqualsCanonicalizing(["area"], $result->getErrors());
    }

    public function testUpdateInvalidPopulation(): void
    {
        $result = $this->districtValidator->validateUpdate(new UpdateDistrictCommand(1, "Foo", 1, 0));

        $this->assertFalse($result->isOk());
        $this->assertEqualsCanonicalizing(["population"], $result->getErrors());
    }

    public function testUpdateMultipleErrors(): void
    {
        $result = $this->districtValidator->validateUpdate(new UpdateDistrictCommand(1, "", 0, 0));

        $this->assertFalse($result->isOk());
        $this->assertEqualsCanonicalizing(["name", "area", "population"], $result->getErrors());
    }
}
