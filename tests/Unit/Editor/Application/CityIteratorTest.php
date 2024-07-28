<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\Application;

use Districts\Editor\Application\CityIterator;
use Districts\Editor\Domain\City;
use Districts\Editor\Domain\CityRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Editor\Application\CityIterator
 */
class CityIteratorTest extends TestCase
{
    /** @var CityRepository&MockObject */
    private CityRepository $cityRepository;

    private CityIterator $cityIterator;

    protected function setUp(): void
    {
        $this->cityRepository = $this->createMock(CityRepository::class);
        $this->cityIterator = new CityIterator($this->cityRepository);
    }

    public function testIterator(): void
    {
        $mockedCities = array_map(
            function (int $id): City {
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
            fn (City $city): int => $city->getId(),
            $iteratorValues,
        );
        $this->assertEqualsCanonicalizing([1, 2, 3], $iteratorIds);
    }
}
