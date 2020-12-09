<?php

declare(strict_types=1);

namespace Districts\Test\Validator;

use Districts\DomainModel\Entity\City;
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
        $this->districtValidator = new DistrictValidator();
    }

    /**
     * @dataProvider validDataProvider
     */
    public function testValid(string $name, float $area, int $population): void
    {
        $result = $this->districtValidator->validate($name, $area, $population);
        $this->assertTrue($result->isOk());
        $this->assertEmpty($result->getErrors());
    }

    public function validDataProvider(): array
    {
        return [
            ["test", 123, 456],
            ["test", 123.4, 567],
            ["test", 0.0001, 1],
        ];
    }

    /**
     * @dataProvider invalidNameDataProvider
     */
    public function testInvalidName(string $name): void
    {
        $result = $this->districtValidator->validate($name, 123, 456);
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
        $result = $this->districtValidator->validate("test", $area, 456);
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
        $result = $this->districtValidator->validate("test", 123, $population);
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

    private function createCityMock(int $id): MockObject
    {
        $mock = $this->createMock(City::class);
        $mock->method("getId")->willReturn($id);
        return $mock;
    }
}
