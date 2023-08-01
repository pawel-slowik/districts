<?php

declare(strict_types=1);

namespace Districts\Test\Editor\Application;

use Districts\Editor\Application\Command\AddDistrictCommand;
use Districts\Editor\Application\Command\UpdateDistrictCommand;
use Districts\Editor\Application\DistrictValidator;
use Districts\Editor\Domain\CityRepository;
use Districts\Editor\Infrastructure\NotFoundInRepositoryException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Editor\Application\DistrictValidator
 */
class DistrictValidatorTest extends TestCase
{
    private DistrictValidator $districtValidator;

    private CityRepository $cityRepository;

    protected function setUp(): void
    {
        $this->cityRepository = $this->createStub(CityRepository::class);
        $this->districtValidator = new DistrictValidator($this->cityRepository);
    }

    public function testAddInvalidName(): void
    {
        $result = $this->districtValidator->validateAdd(new AddDistrictCommand(1, "", 1, 1));

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
