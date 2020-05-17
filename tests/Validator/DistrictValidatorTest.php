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

    public function testExceptionOnReadingInvalidData(): void
    {
        $result = $this->districtValidator->validate([]);
        $this->expectException(\LogicException::class);
        $result->getValidatedData();
    }

    /**
     * @dataProvider validDataProvider
     */
    public function testValid($input): void
    {
        $result = $this->districtValidator->validate($input);
        $this->assertTrue($result->isOk());
        $this->assertEmpty($result->getErrors());
        $this->assertNotEmpty($result->getValidatedData());
    }

    public function validDataProvider(): array
    {
        return [
            [
                [
                    "name" => "test",
                    "area" => "123",
                    "population" => "456",
                ],
            ],
            [
                [
                    "name" => "test",
                    "area" => 123,
                    "population" => 456,
                ],
            ],
            [
                [
                    "name" => "test",
                    "area" => "123.4",
                    "population" => "567",
                ],
            ],
            [
                [
                    "name" => "test",
                    "area" => 123.4,
                    "population" => 567,
                ],
            ],
            [
                [
                    "name" => "test",
                    "area" => 0.0001,
                    "population" => 1,
                ],
            ],
        ];
    }

    /**
     * @dataProvider invalidNameDataProvider
     */
    public function testInvalidName($input): void
    {
        $result = $this->districtValidator->validate($input);
        $this->assertFalse($result->isOk());
        $this->assertContains("name", $result->getErrors());
    }

    public function invalidNameDataProvider(): array
    {
        return [
            [
                [
                    "name" => "",
                    "area" => "123",
                    "population" => "456",
                ],
            ],
            [
                [
                    "area" => "123",
                    "population" => "456",
                ],
            ],
        ];
    }

    /**
     * @dataProvider invalidAreaDataProvider
     */
    public function testinValidArea($input): void
    {
        $result = $this->districtValidator->validate($input);
        $this->assertFalse($result->isOk());
        $this->assertContains("area", $result->getErrors());
    }

    public function invalidAreaDataProvider(): array
    {
        return [
            [
                [
                    "name" => "test",
                    "area" => "",
                    "population" => "456",
                ],
            ],
            [
                [
                    "name" => "test",
                    "area" => "0",
                    "population" => "456",
                ],
            ],
            [
                [
                    "name" => "test",
                    "area" => "-1",
                    "population" => "456",
                ],
            ],
            [
                [
                    "name" => "test",
                    "area" => "foo",
                    "population" => "456",
                ],
            ],
            [
                [
                    "name" => "test",
                    "population" => "456",
                ],
            ],
        ];
    }

    /**
     * @dataProvider invalidPopulationDataProvider
     */
    public function testinValidPopulation($input): void
    {
        $result = $this->districtValidator->validate($input);
        $this->assertFalse($result->isOk());
        $this->assertContains("population", $result->getErrors());
    }

    public function invalidPopulationDataProvider(): array
    {
        return [
            [
                [
                    "name" => "test",
                    "area" => "123",
                    "population" => "",
                ],
            ],
            [
                [
                    "name" => "test",
                    "area" => "123",
                    "population" => "0",
                ],
            ],
            [
                [
                    "name" => "test",
                    "area" => "123",
                    "population" => "-1",
                ],
            ],
            [
                [
                    "name" => "test",
                    "area" => "123",
                    "population" => "0.1",
                ],
            ],
            [
                [
                    "name" => "test",
                    "area" => "123",
                    "population" => "bar",
                ],
            ],
            [
                [
                    "name" => "test",
                    "area" => "123",
                ],
            ],
        ];
    }
}