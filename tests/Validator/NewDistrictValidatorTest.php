<?php

declare(strict_types=1);

namespace Test\Validator;

use Validator\DistrictValidator;
use Validator\NewDistrictValidator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Validator\NewDistrictValidator
 */
class NewDistrictValidatorTest extends TestCase
{
    private $newDistrictValidator;

    protected function setUp(): void
    {
        $this->newDistrictValidator = new NewDistrictValidator(new DistrictValidator(), [1, 3]);
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
}
