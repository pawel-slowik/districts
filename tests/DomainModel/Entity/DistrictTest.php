<?php

declare(strict_types=1);

namespace Districts\Test\DomainModel\Entity;

use Districts\DomainModel\Entity\City;
use Districts\DomainModel\Entity\District;
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

    /**
     * @var District
     */
    private $district;

    protected function setUp(): void
    {
        $this->city = $this->createStub(City::class);

        $this->district = new District($this->city, "foo", 10.1, 202);
    }

    public function testGetters(): void
    {
        $this->assertSame("foo", $this->district->getName());
        $this->assertSame(10.1, $this->district->getArea());
        $this->assertSame(202, $this->district->getPopulation());
        $this->assertSame($this->city, $this->district->getCity());
    }

    public function testSetters(): void
    {
        $this->district->setName("bar");
        $this->district->setArea(30.3);
        $this->district->setPopulation(404);

        $this->assertSame("bar", $this->district->getName());
        $this->assertSame(30.3, $this->district->getArea());
        $this->assertSame(404, $this->district->getPopulation());
    }
}
