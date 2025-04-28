<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\Application;

use Districts\Core\Domain\City;
use Districts\Core\Domain\CityRepository;
use Districts\Editor\Application\CityIterator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(CityIterator::class)]
final class CityIteratorTest extends TestCase
{
    private CityRepository&MockObject $cityRepository;

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
            static fn (City $city): int => $city->getId(),
            $iteratorValues,
        );
        $this->assertEqualsCanonicalizing([1, 2, 3], $iteratorIds);
    }
}
