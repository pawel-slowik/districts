<?php

declare(strict_types=1);

namespace Test\Service;

use DomainModel\Entity\City;
use Service\CityIterator;
use Repository\CityRepository;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Service\CityIterator
 */
class CityIteratorTest extends TestCase
{
    private $cityRepository;

    private $cityIterator;

    protected function setUp(): void
    {
        $this->cityRepository = $this->createMock(CityRepository::class);
        $this->cityIterator = new CityIterator($this->cityRepository);
    }

    public function testIterator(): void
    {
        $mockedCities = array_map(
            function (int $id): MockObject {
                $mock = $this->createMock(City::class);
                $mock->method("getId")->willReturn($id);
                return $mock;
            },
            [3, 2, 1],
        );
        $this->cityRepository->method("list")->willReturn($mockedCities);

        $iteratorValues = [];
        foreach ($this->cityIterator as $value) {
            $iteratorValues[] = $value;
        }

        $this->assertCount(3, $iteratorValues);
        $this->assertContainsOnlyInstancesOf(City::class, $iteratorValues);

        $iteratorIds = array_map(
            function (City $city): int {
                return $city->getId();
            },
            $iteratorValues,
        );
        $this->assertEqualsCanonicalizing([1, 2, 3], $iteratorIds);
    }
}