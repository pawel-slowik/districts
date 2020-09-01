<?php

declare(strict_types=1);

namespace Test\Validator;

use DomainModel\Entity\City;
use Service\CityIterator;
use Validator\DistrictValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Validator\DistrictValidator
 */
class DistrictValidatorTest extends TestCase
{
    /**
     * @var DistrictValidator
     */
    private $districtValidator;

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
        $this->districtValidator = new DistrictValidator($cityIterator);
    }

    /**
     * @dataProvider validDataProvider
     */
    public function testValid($city, $name, $area, $population): void
    {
        $result = $this->districtValidator->validate($city, $name, $area, $population);
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
     * @dataProvider invalidNameDataProvider
     */
    public function testInvalidName($name): void
    {
        $result = $this->districtValidator->validate(1, $name, 123, 456);
        $this->assertFalse($result->isOk());
        $this->assertContains("name", $result->getErrors());
        $this->assertCount(1, $result->getErrors());
    }

    public function invalidNameDataProvider(): array
    {
        return [
            [null],
            [""],
        ];
    }

    /**
     * @dataProvider invalidAreaDataProvider
     */
    public function testinValidArea($area): void
    {
        $result = $this->districtValidator->validate(1, "test", $area, 456);
        $this->assertFalse($result->isOk());
        $this->assertContains("area", $result->getErrors());
        $this->assertCount(1, $result->getErrors());
    }

    public function invalidAreaDataProvider(): array
    {
        return [
            [null],
            [""],
            [0],
            [-1],
            ["foo"],
        ];
    }

    /**
     * @dataProvider invalidPopulationDataProvider
     */
    public function testinValidPopulation($population): void
    {
        $result = $this->districtValidator->validate(1, "test", 123, $population);
        $this->assertFalse($result->isOk());
        $this->assertContains("population", $result->getErrors());
        $this->assertCount(1, $result->getErrors());
    }

    public function invalidPopulationDataProvider(): array
    {
        return [
            [null],
            [""],
            [0],
            [-1],
            [0.1],
            ["bar"],
        ];
    }

    /**
     * @dataProvider invalidCityDataProvider
     */
    public function testinValidCity($city): void
    {
        $result = $this->districtValidator->validate($city, "test", 123, 456);
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
