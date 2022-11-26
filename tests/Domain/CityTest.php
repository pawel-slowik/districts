<?php

declare(strict_types=1);

namespace Districts\Test\Domain;

use Districts\Domain\Area;
use Districts\Domain\City;
use Districts\Domain\Exception\DistrictNotFoundException;
use Districts\Domain\Name;
use Districts\Domain\Population;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Domain\City
 */
class CityTest extends TestCase
{
    public function testName(): void
    {
        $city = new City("test");

        $this->assertSame("test", $city->getName());
    }

    public function testUpdateThrowsExceptionOnUnknownDistrictId(): void
    {
        $city = new City("test");

        $this->expectException(DistrictNotFoundException::class);

        $city->updateDistrict(1, new Name("test"), new Area(123.4), new Population(5678));
    }

    public function testRemoveThrowsExceptionOnUnknownDistrictId(): void
    {
        $city = new City("test");

        $this->expectException(DistrictNotFoundException::class);

        $city->removeDistrict(1);
    }
}
