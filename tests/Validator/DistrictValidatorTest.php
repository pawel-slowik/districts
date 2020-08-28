<?php

declare(strict_types=1);

namespace Test\Validator;

use Validator\DistrictValidator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Validator\DistrictValidator
 * @covers \Validator\ValidationResult
 */
class DistrictValidatorTest extends TestCase
{
    private $districtValidator;

    protected function setUp(): void
    {
        $this->districtValidator = new DistrictValidator();
    }

    /**
     * @dataProvider validDataProvider
     */
    public function testValid($name, $area, $population): void
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
    public function testInvalidName($name): void
    {
        $result = $this->districtValidator->validate($name, 123, 456);
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
        $result = $this->districtValidator->validate("test", $area, 456);
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
        $result = $this->districtValidator->validate("test", 123, $population);
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
}
