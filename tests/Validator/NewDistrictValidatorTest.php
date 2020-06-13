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

    public function validDataProvider(): array
    {
        return [
            [
                [
                    "name" => "test",
                    "area" => 123,
                    "population" => 456,
                    "city" => 3,
                ],
            ],
            [
                [
                    "name" => "test",
                    "area" => 123.4,
                    "population" => 567,
                    "city" => 3,
                ],
            ],
            [
                [
                    "name" => "test",
                    "area" => 0.0001,
                    "population" => 1,
                    "city" => 1,
                ],
            ],
        ];
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

    public function invalidNameDataProvider(): array
    {
        return [
            [
                [
                    "name" => null,
                    "area" => 123,
                    "population" => 456,
                    "city" => 1,
                ],
            ],
            [
                [
                    "name" => "",
                    "area" => 123,
                    "population" => 456,
                    "city" => 1,
                ],
            ],
            [
                [
                    "area" => 123,
                    "population" => 456,
                    "city" => 1,
                ],
            ],
        ];
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

    public function invalidAreaDataProvider(): array
    {
        return [
            [
                [
                    "name" => "test",
                    "area" => null,
                    "population" => 456,
                    "city" => 1,
                ],
            ],
            [
                [
                    "name" => "test",
                    "area" => "",
                    "population" => 456,
                    "city" => 1,
                ],
            ],
            [
                [
                    "name" => "test",
                    "area" => 0,
                    "population" => 456,
                    "city" => 1,
                ],
            ],
            [
                [
                    "name" => "test",
                    "area" => -1,
                    "population" => 456,
                    "city" => 1,
                ],
            ],
            [
                [
                    "name" => "test",
                    "area" => "foo",
                    "population" => 456,
                    "city" => 1,
                ],
            ],
            [
                [
                    "name" => "test",
                    "population" => 456,
                    "city" => 1,
                ],
            ],
        ];
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

    public function invalidPopulationDataProvider(): array
    {
        return [
            [
                [
                    "name" => "test",
                    "area" => 123,
                    "population" => null,
                    "city" => 1,
                ],
            ],
            [
                [
                    "name" => "test",
                    "area" => 123,
                    "population" => "",
                    "city" => 1,
                ],
            ],
            [
                [
                    "name" => "test",
                    "area" => 123,
                    "population" => 0,
                    "city" => 1,
                ],
            ],
            [
                [
                    "name" => "test",
                    "area" => 123,
                    "population" => -1,
                    "city" => 1,
                ],
            ],
            [
                [
                    "name" => "test",
                    "area" => 123,
                    "population" => 0.1,
                    "city" => 1,
                ],
            ],
            [
                [
                    "name" => "test",
                    "area" => 123,
                    "population" => "bar",
                    "city" => 1,
                ],
            ],
            [
                [
                    "name" => "test",
                    "area" => 123,
                    "city" => 1,
                ],
            ],
        ];
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

    public function invalidCityDataProvider(): array
    {
        return [
            [
                [
                    "name" => "test",
                    "area" => 123,
                    "population" => 456,
                    "city" => null,
                ],
            ],
            [
                [
                    "name" => "test",
                    "area" => 123,
                    "population" => 456,
                    "city" => "2",
                ],
            ],
            [
                [
                    "name" => "test",
                    "area" => 123,
                    "population" => 456,
                    "city" => 2,
                ],
            ],
            [
                [
                    "name" => "test",
                    "area" => 123,
                    "population" => 456,
                    "city" => "foo",
                ],
            ],
            [
                [
                    "name" => "test",
                    "area" => 123,
                    "population" => 456,
                ],
            ],
        ];
    }
}
