<?php

declare(strict_types=1);

namespace Districts\Test\DomainModel\Entity;

use Districts\DomainModel\Entity\City;
use Districts\DomainModel\NotFoundException;
use Districts\DomainModel\VO\Area;
use Districts\DomainModel\VO\Name;
use Districts\DomainModel\VO\Population;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\DomainModel\Entity\City
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

        $this->expectException(NotFoundException::class);

        $city->updateDistrict(1, new Name("test"), new Area(123.4), new Population(5678));
    }

    public function testRemoveThrowsExceptionOnUnknownDistrictId(): void
    {
        $city = new City("test");

        $this->expectException(NotFoundException::class);

        $city->removeDistrict(1);
    }
}
