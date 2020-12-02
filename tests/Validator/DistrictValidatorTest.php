<?php

declare(strict_types=1);

namespace Districts\Test\Validator;

use Districts\DomainModel\Entity\City;
use Districts\Service\CityIterator;
use Districts\Validator\DistrictValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Validator\DistrictValidator
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
    public function testValid(int $cityId, string $name, float $area, int $population): void
    {
        $result = $this->districtValidator->validate($cityId, $name, $area, $population);
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
    public function testInvalidName(string $name): void
    {
        $result = $this->districtValidator->validate(1, $name, 123, 456);
        $this->assertFalse($result->isOk());
        $this->assertContains("name", $result->getErrors());
        $this->assertCount(1, $result->getErrors());
    }

    public function invalidNameDataProvider(): array
    {
        return [
            [""],
        ];
    }

    /**
     * @dataProvider invalidAreaDataProvider
     */
    public function testinValidArea(float $area): void
    {
        $result = $this->districtValidator->validate(1, "test", $area, 456);
        $this->assertFalse($result->isOk());
        $this->assertContains("area", $result->getErrors());
        $this->assertCount(1, $result->getErrors());
    }

    public function invalidAreaDataProvider(): array
    {
        return [
            [0],
            [-1],
        ];
    }

    /**
     * @dataProvider invalidPopulationDataProvider
     */
    public function testinValidPopulation(int $population): void
    {
        $result = $this->districtValidator->validate(1, "test", 123, $population);
        $this->assertFalse($result->isOk());
        $this->assertContains("population", $result->getErrors());
        $this->assertCount(1, $result->getErrors());
    }

    public function invalidPopulationDataProvider(): array
    {
        return [
            [0],
            [-1],
        ];
    }

    /**
     * @dataProvider invalidCityDataProvider
     */
    public function testinValidCity(int $cityId): void
    {
        $result = $this->districtValidator->validate($cityId, "test", 123, 456);
        $this->assertFalse($result->isOk());
        $this->assertContains("city", $result->getErrors());
        $this->assertCount(1, $result->getErrors());
    }

    public function invalidCityDataProvider(): array
    {
        return [
            [2],
        ];
    }

    private function createCityMock(int $id): MockObject
    {
        $mock = $this->createMock(City::class);
        $mock->method("getId")->willReturn($id);
        return $mock;
    }
}
