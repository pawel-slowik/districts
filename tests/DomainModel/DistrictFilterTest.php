<?php

declare(strict_types=1);

namespace Districts\Test\DomainModel;

use Districts\DomainModel\DistrictFilter;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\DomainModel\DistrictFilter
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
     * @dataProvider validDataProvider
     *
     * @phpstan-param mixed $value
     */
    public function testValid(int $type, $value): void
    {
        $filter = new DistrictFilter($type, $value);
        $this->assertInstanceOf(DistrictFilter::class, $filter);
    }

    public function validDataProvider(): array
    {
        return [
            [DistrictFilter::TYPE_CITY, "foo"],
            [DistrictFilter::TYPE_NAME, "foo"],
            [DistrictFilter::TYPE_AREA, [1, 2]],
            [DistrictFilter::TYPE_AREA, [1.1, 2.2]],
            [DistrictFilter::TYPE_AREA, [1.1, 2]],
            [DistrictFilter::TYPE_AREA, [1, 2.2]],
            [DistrictFilter::TYPE_POPULATION, [1, 2]],
            [DistrictFilter::TYPE_POPULATION, [1.1, 2.2]],
            [DistrictFilter::TYPE_POPULATION, [1.1, 2]],
            [DistrictFilter::TYPE_POPULATION, [1, 2.2]],
        ];
    }

    /**
     * @dataProvider invalidDataProvider
     *
     * @phpstan-param mixed $value
     */
    public function testInvalid(int $type, $value): void
    {
        $this->expectException(InvalidArgumentException::class);
        new DistrictFilter($type, $value);
    }

    public function invalidDataProvider(): array
    {
        return [
            "invalid type" => [0, "foo"],
            "not a string" => [DistrictFilter::TYPE_CITY, 1],
            "invalid string" => [DistrictFilter::TYPE_CITY, ""],
            "range not an array" => [DistrictFilter::TYPE_AREA, "1-1"],
            "range missing element" => [DistrictFilter::TYPE_AREA, [1]],
            "range extra element" => [DistrictFilter::TYPE_AREA, [1, 2, 3]],
            "range missing begin" => [DistrictFilter::TYPE_AREA, ["a" => 1, 1 => 2]],
            "range missing end" => [DistrictFilter::TYPE_AREA, [0 => 1, "b" => 2]],
            "range begin invalid type" => [DistrictFilter::TYPE_AREA, [0 => "1", 1 => 2]],
            "range end invalid type" => [DistrictFilter::TYPE_AREA, [0 => 1, 1 => "2"]],
            "range negative begin" => [DistrictFilter::TYPE_AREA, [0 => -1, 1 => 2]],
            "range negative end" => [DistrictFilter::TYPE_AREA, [0 => 1, 1 => -2]],
            "range end lower than begin" => [DistrictFilter::TYPE_AREA, [0 => 2.001, 1 => 2]],
        ];
    }
}
