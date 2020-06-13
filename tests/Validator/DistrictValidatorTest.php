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
    public function testValid($input): void
    {
        $result = $this->districtValidator->validate($input);
        $this->assertTrue($result->isOk());
        $this->assertEmpty($result->getErrors());
    }

    public function validDataProvider(): iterable
    {
        yield [[
            "name" => "test",
            "area" => 123,
            "population" => 456,
        ]];

        yield [[
            "name" => "test",
            "area" => 123.4,
            "population" => 567,
        ]];

        yield [[
            "name" => "test",
            "area" => 0.0001,
            "population" => 1,
        ]];
    }

    /**
     * @dataProvider invalidNameDataProvider
     */
    public function testInvalidName($input): void
    {
        $result = $this->districtValidator->validate($input);
        $this->assertFalse($result->isOk());
        $this->assertContains("name", $result->getErrors());
        $this->assertCount(1, $result->getErrors());
    }

    public function invalidNameDataProvider(): iterable
    {
        $input = [
            "area" => 123,
            "population" => 456,
        ];

        $input["name"] = null;
        yield [$input];

        $input["name"] = "";
        yield [$input];

        unset($input["name"]);
        yield [$input];
    }

    /**
     * @dataProvider invalidAreaDataProvider
     */
    public function testinValidArea($input): void
    {
        $result = $this->districtValidator->validate($input);
        $this->assertFalse($result->isOk());
        $this->assertContains("area", $result->getErrors());
        $this->assertCount(1, $result->getErrors());
    }

    public function invalidAreaDataProvider(): iterable
    {
        $input = [
            "name" => "test",
            "population" => 456,
        ];

        $input["area"] = null;
        yield [$input];

        $input["area"] = "";
        yield [$input];

        $input["area"] = 0;
        yield [$input];

        $input["area"] = -1;
        yield [$input];

        $input["area"] = "foo";
        yield [$input];

        unset($input["area"]);
        yield [$input];
    }

    /**
     * @dataProvider invalidPopulationDataProvider
     */
    public function testinValidPopulation($input): void
    {
        $result = $this->districtValidator->validate($input);
        $this->assertFalse($result->isOk());
        $this->assertContains("population", $result->getErrors());
        $this->assertCount(1, $result->getErrors());
    }

    public function invalidPopulationDataProvider(): iterable
    {
        $input = [
            "name" => "test",
            "area" => 123,
        ];

        $input["population"] = null;
        yield [$input];

        $input["population"] = "";
        yield [$input];

        $input["population"] = 0;
        yield [$input];

        $input["population"] = -1;
        yield [$input];

        $input["population"] = 0.1;
        yield [$input];

        $input["population"] = "bar";
        yield [$input];

        unset($input["population"]);
        yield [$input];
    }
}
