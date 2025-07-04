<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Core\Domain;

use Districts\Core\Domain\Area;
use Districts\Core\Domain\City;
use Districts\Core\Domain\Exception\DistrictNotFoundException;
use Districts\Core\Domain\Exception\DuplicateDistrictNameException;
use Districts\Core\Domain\Name;
use Districts\Core\Domain\Population;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(City::class)]
final class CityTest extends TestCase
{
    public function testName(): void
    {
        $city = new City("test");

        $this->assertSame("test", $city->getName());
    }

    public function testUpdateThrowsExceptionOnUnknownDistrictName(): void
    {
        $city = new City("test");
        $city->addDistrict(new Name("test"), new Area(123.4), new Population(5678));

        $this->expectException(DistrictNotFoundException::class);

        $city->updateDistrict(new Name("not test"), new Name("updated test"), new Area(123.4), new Population(5678));
    }

    public function testUpdateThrowsExceptionOnDuplicateDistrictName(): void
    {
        $city = new City("test");
        $city->addDistrict(new Name("test 1"), new Area(123.4), new Population(5678));
        $city->addDistrict(new Name("test 2"), new Area(123.4), new Population(5678));

        $this->expectException(DuplicateDistrictNameException::class);

        $city->updateDistrict(new Name("test 2"), new Name("test 1"), new Area(123.4), new Population(5678));
    }

    public function testUpdateAllowsUnchangedDistrictName(): void
    {
        $city = new City("test");
        $city->addDistrict(new Name("test"), new Area(123.4), new Population(5678));

        $city->updateDistrict(new Name("test"), new Name("test"), new Area(123.4), new Population(5678));

        $this->addToAssertionCount(1); // does not throw an exception
    }

    public function testAddThrowsExceptionOnDuplicateDistrictName(): void
    {
        $city = new City("test");
        $city->addDistrict(new Name("test"), new Area(123.4), new Population(5678));

        $this->expectException(DuplicateDistrictNameException::class);

        $city->addDistrict(new Name("test"), new Area(234.5), new Population(6789));
    }

    public function testRemove(): void
    {
        $city = new City("test");
        $city->addDistrict(new Name("test"), new Area(123.4), new Population(5678));

        $city->removeDistrict(new Name("test"));

        $this->assertFalse($city->hasDistrictWithName(new Name("test")));
    }

    public function testRemoveThrowsExceptionOnUnknownDistrictName(): void
    {
        $city = new City("test");
        $city->addDistrict(new Name("test"), new Area(123.4), new Population(5678));

        $this->expectException(DistrictNotFoundException::class);

        $city->removeDistrict(new Name("not test"));
    }

    public function testHasDistrictWithName(): void
    {
        $city = new City("test");
        $city->addDistrict(new Name("foo"), new Area(123.4), new Population(5678));

        $this->assertTrue($city->hasDistrictWithName(new Name("foo")));
    }

    public function testDoesNotHaveDistrictWithName(): void
    {
        $city = new City("test");
        $city->addDistrict(new Name("foo"), new Area(123.4), new Population(5678));

        $this->assertFalse($city->hasDistrictWithName(new Name("bar")));
    }
}
