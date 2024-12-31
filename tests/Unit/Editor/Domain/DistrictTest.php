<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\Domain;

use Districts\Editor\Domain\Area;
use Districts\Editor\Domain\City;
use Districts\Editor\Domain\District;
use Districts\Editor\Domain\Name;
use Districts\Editor\Domain\Population;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(District::class)]
class DistrictTest extends TestCase
{
    private City $city;

    private District $district;

    protected function setUp(): void
    {
        $this->city = $this->createStub(City::class);

        $this->district = new District($this->city, new Name("foo"), new Area(10.1), new Population(202));
    }

    public function testGetters(): void
    {
        $this->assertObjectEquals(new Name("foo"), $this->district->getName());
        $this->assertObjectEquals(new Area(10.1), $this->district->getArea());
        $this->assertObjectEquals(new Population(202), $this->district->getPopulation());
        $this->assertSame($this->city, $this->district->getCity());
    }

    public function testSetters(): void
    {
        $this->district->setName(new Name("bar"));
        $this->district->setArea(new Area(30.3));
        $this->district->setPopulation(new Population(404));

        $this->assertObjectEquals(new Name("bar"), $this->district->getName());
        $this->assertObjectEquals(new Area(30.3), $this->district->getArea());
        $this->assertObjectEquals(new Population(404), $this->district->getPopulation());
    }
}
