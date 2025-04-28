<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Core\Domain;

use Districts\Core\Domain\Area;
use Districts\Core\Domain\City;
use Districts\Core\Domain\District;
use Districts\Core\Domain\Name;
use Districts\Core\Domain\Population;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(District::class)]
final class DistrictTest extends TestCase
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

    public function testUpdate(): void
    {
        $this->district->update(new Name("bar"), new Area(30.3), new Population(404));

        $this->assertObjectEquals(new Name("bar"), $this->district->getName());
        $this->assertObjectEquals(new Area(30.3), $this->district->getArea());
        $this->assertObjectEquals(new Population(404), $this->district->getPopulation());
    }
}
