<?php

declare(strict_types=1);

namespace Test\Validator;

use DomainModel\Entity\City;
use Service\CityIterator;
use Validator\DistrictValidator;
use Validator\NewDistrictValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Validator\NewDistrictValidator
 */
class NewDistrictValidatorTest extends TestCase
{
    private $newDistrictValidator;

    protected function setUp(): void
    {
        $cityIterator = $this->createMock(CityIterator::class);
        $mockedCities = array_map([$this, "createCityMock"], [1, 3]);
        $cityIterator
            ->method("getIterator")
            ->will(
                $this->returnCallback(
                    function () use ($mockedCities) {
                        return new \ArrayIterator($mockedCities);
                    }
                )
            );
        $this->newDistrictValidator = new NewDistrictValidator(new DistrictValidator(), $cityIterator);
    }

    /**
     * @dataProvider validDataProvider
     */
    public function testValid($city, $name, $area, $population): void
    {
        $result = $this->newDistrictValidator->validate($city, $name, $area, $population);
        $this->assertTrue($result->isOk());
        $this->assertEmpty($result->getErrors());
    }

    public function validDataProvider(): array
    {
        return [
            [3, "test", 123, 456],
            [3, "test", 123.4, 567],
            [1, "test", 0.0001, 1],
        ];
    }

    /**
     * @dataProvider \Test\Validator\DistrictValidatorTest::invalidNameDataProvider
     */
    public function testInvalidName($name): void
    {
        $result = $this->newDistrictValidator->validate(1, $name, 123, 456);
        $this->assertFalse($result->isOk());
        $this->assertContains("name", $result->getErrors());
        $this->assertCount(1, $result->getErrors());
    }

    /**
     * @dataProvider \Test\Validator\DistrictValidatorTest::invalidAreaDataProvider
     */
    public function testinValidArea($area): void
    {
        $result = $this->newDistrictValidator->validate(1, "test", $area, 456);
        $this->assertFalse($result->isOk());
        $this->assertContains("area", $result->getErrors());
        $this->assertCount(1, $result->getErrors());
    }

    /**
     * @dataProvider \Test\Validator\DistrictValidatorTest::invalidPopulationDataProvider
     */
    public function testinValidPopulation($population): void
    {
        $result = $this->newDistrictValidator->validate(1, "test", 123, $population);
        $this->assertFalse($result->isOk());
        $this->assertContains("population", $result->getErrors());
        $this->assertCount(1, $result->getErrors());
    }

    /**
     * @dataProvider invalidCityDataProvider
     */
    public function testinValidCity($city): void
    {
        $result = $this->newDistrictValidator->validate($city, "test", 123, 456);
        $this->assertFalse($result->isOk());
        $this->assertContains("city", $result->getErrors());
        $this->assertCount(1, $result->getErrors());
    }

    public function invalidCityDataProvider(): array
    {
        return [
            [null],
            [2],
            ["foo"],
            ["1"],
        ];
    }

    private function createCityMock(int $id): MockObject
    {
        $mock = $this->createMock(City::class);
        $mock->method("getId")->willReturn($id);
        return $mock;
    }
}
