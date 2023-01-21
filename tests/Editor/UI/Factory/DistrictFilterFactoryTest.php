<?php

declare(strict_types=1);

namespace Districts\Test\Editor\UI\Factory;

use Districts\Editor\Domain\DistrictFilter\AreaFilter;
use Districts\Editor\Domain\DistrictFilter\CityNameFilter;
use Districts\Editor\Domain\DistrictFilter\NameFilter;
use Districts\Editor\Domain\DistrictFilter\PopulationFilter;
use Districts\Editor\UI\Factory\DistrictFilterFactory;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Editor\UI\Factory\DistrictFilterFactory
 */
class DistrictFilterFactoryTest extends TestCase
{
    private DistrictFilterFactory $districtFilterFactory;

    protected function setUp(): void
    {
        $this->districtFilterFactory = new DistrictFilterFactory();
    }

    /**
     * @dataProvider createNullDataProvider
     */
    public function testCreateNull(?string $column, ?string $value): void
    {
        $filter = $this->districtFilterFactory->createFromRequestInput($column, $value);
        $this->assertNull($filter);
    }

    public function createNullDataProvider(): array
    {
        return [
            "null value" => [
                "city",
                null,
            ],
            "empty value" => [
                "city",
                "",
            ],
            "null column" => [
                null,
                "foo",
            ],
            "invalid column" => [
                "bar",
                "baz",
            ],
        ];
    }

    /**
     * @dataProvider createDataProvider
     */
    public function testCreate(?string $inputColumn, ?string $inputValue, string $expectedClass): void
    {
        $filter = $this->districtFilterFactory->createFromRequestInput($inputColumn, $inputValue);
        $this->assertInstanceOf($expectedClass, $filter);
    }

    public function createDataProvider(): array
    {
        return [
            ["city", "foo", CityNameFilter::class],
            ["name", "bar", NameFilter::class],
            ["area", "1", AreaFilter::class],
            ["area", "1-2", AreaFilter::class],
            ["population", "3", PopulationFilter::class],
            ["population", "3-4", PopulationFilter::class],
        ];
    }
}
