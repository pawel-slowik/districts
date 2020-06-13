<?php

declare(strict_types=1);

namespace Test\Validator;

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
        $this->newDistrictValidator = new NewDistrictValidator([1, 3]);
    }

    /**
     * @dataProvider validDataProvider
     */
    public function testValid($input): void
    {
        $result = $this->newDistrictValidator->validate($input);
        $this->assertTrue($result->isOk());
        $this->assertEmpty($result->getErrors());
    }

    public function validDataProvider(): iterable
    {
        yield [[
            "name" => "test",
            "area" => 123,
            "population" => 456,
            "city" => 3,
        ]];

        yield [[
            "name" => "test",
            "area" => 123.4,
            "population" => 567,
            "city" => 3,
        ]];

        yield [[
            "name" => "test",
            "area" => 0.0001,
            "population" => 1,
            "city" => 1,
        ]];
    }

    /**
     * @dataProvider invalidNameDataProvider
     */
    public function testInvalidName($input): void
    {
        $result = $this->newDistrictValidator->validate($input);
        $this->assertFalse($result->isOk());
        $this->assertContains("name", $result->getErrors());
        $this->assertCount(1, $result->getErrors());
    }

    public function invalidNameDataProvider(): iterable
    {
        $input = [
            "area" => 123,
            "population" => 456,
            "city" => 1,
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
        $result = $this->newDistrictValidator->validate($input);
        $this->assertFalse($result->isOk());
        $this->assertContains("area", $result->getErrors());
        $this->assertCount(1, $result->getErrors());
    }

    public function invalidAreaDataProvider(): iterable
    {
        $input = [
            "name" => "test",
            "population" => 456,
            "city" => 1,
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
        $result = $this->newDistrictValidator->validate($input);
        $this->assertFalse($result->isOk());
        $this->assertContains("population", $result->getErrors());
        $this->assertCount(1, $result->getErrors());
    }

    public function invalidPopulationDataProvider(): iterable
    {
        $input = [
            "name" => "test",
            "area" => 123,
            "city" => 1,
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

    /**
     * @dataProvider invalidCityDataProvider
     */
    public function testinValidCity($input): void
    {
        $result = $this->newDistrictValidator->validate($input);
        $this->assertFalse($result->isOk());
        $this->assertContains("city", $result->getErrors());
        $this->assertCount(1, $result->getErrors());
    }

    public function invalidCityDataProvider(): iterable
    {
        $input = [
            "name" => "test",
            "area" => 123,
            "population" => 456,
        ];

        $input["city"] = null;
        yield [$input];

        $input["city"] = 2;
        yield [$input];

        $input["city"] = "foo";
        yield [$input];

        $input["city"] = "1";
        yield [$input];

        unset($input["city"]);
        yield [$input];
    }
}
