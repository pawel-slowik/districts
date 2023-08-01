<?php

declare(strict_types=1);

namespace Districts\Test\Editor\Application;

use Districts\Editor\Application\Command\AddDistrictCommand;
use Districts\Editor\Application\DistrictValidator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Editor\Application\DistrictValidator
 */
class DistrictValidatorTest extends TestCase
{
    private DistrictValidator $districtValidator;

    protected function setUp(): void
    {
        $this->districtValidator = new DistrictValidator();
    }

    public function testInvalidName(): void
    {
        $result = $this->districtValidator->validate(new AddDistrictCommand(1, "", 1, 1));

        $this->assertFalse($result->isOk());
        $this->assertEqualsCanonicalizing(["name"], $result->getErrors());
    }

    public function testInvalidArea(): void
    {
        $result = $this->districtValidator->validate(new AddDistrictCommand(1, "Foo", 0, 1));

        $this->assertFalse($result->isOk());
        $this->assertEqualsCanonicalizing(["area"], $result->getErrors());
    }

    public function testInvalidPopulation(): void
    {
        $result = $this->districtValidator->validate(new AddDistrictCommand(1, "Foo", 1, 0));

        $this->assertFalse($result->isOk());
        $this->assertEqualsCanonicalizing(["population"], $result->getErrors());
    }

    public function testMultipleErrors(): void
    {
        $result = $this->districtValidator->validate(new AddDistrictCommand(1, "", 0, 0));

        $this->assertFalse($result->isOk());
        $this->assertEqualsCanonicalizing(["name", "area", "population"], $result->getErrors());
    }
}
