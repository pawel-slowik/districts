<?php

declare(strict_types=1);

namespace Districts\Test\DomainModel\Entity;

use Districts\DomainModel\Area;
use Districts\DomainModel\Entity\City;
use Districts\DomainModel\Entity\District;
use Districts\DomainModel\Name;
use Districts\DomainModel\Population;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\DomainModel\Entity\District
 */
class DistrictTest extends TestCase
{
    /**
     * @var City|Stub
     */
    private $city;

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
