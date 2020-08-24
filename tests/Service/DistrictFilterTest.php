<?php

declare(strict_types=1);

namespace Test\Service;

use Service\DistrictFilter;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Service\DistrictFilter
 */
class DistrictFilterTest extends TestCase
{
    public function testGetters(): void
    {
        $filter = new DistrictFilter(DistrictFilter::TYPE_CITY, "test");
        $this->assertSame(DistrictFilter::TYPE_CITY, $filter->getType());
        $this->assertSame("test", $filter->getValue());
    }

    /**
     * @dataProvider createNullDataProvider
     */
    public function testCreateNull(?string $column, ?string $value): void
    {
        $filter = DistrictFilter::createFromRequestInput($column, $value);
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
    public function testCreate(
        ?string $inputColumn,
        ?string $inputValue,
        int $expectedType,
        $expectedValue
    ): void {
        $filter = DistrictFilter::createFromRequestInput($inputColumn, $inputValue);
        $this->assertInstanceOf(DistrictFilter::class, $filter);
        $this->assertSame($expectedType, $filter->getType());
        $this->assertSame($expectedValue, $filter->getValue());
    }

    public function createDataProvider(): array
    {
        return [
            [
                "city", "foo",
                DistrictFilter::TYPE_CITY, "foo",
            ],
            [
                "name", "bar",
                DistrictFilter::TYPE_NAME, "bar",
            ],
            [
                "area", "1-2",
                DistrictFilter::TYPE_AREA, [1.0, 2.0],
            ],
            [
                "population", "3-4",
                DistrictFilter::TYPE_POPULATION, [3.0, 4.0],
            ],
        ];
    }
}
