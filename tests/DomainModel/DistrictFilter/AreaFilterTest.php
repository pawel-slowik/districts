<?php

declare(strict_types=1);

namespace Districts\Test\DomainModel\DistrictFilter;

use Districts\DomainModel\DistrictFilter\AreaFilter;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\DomainModel\DistrictFilter\AreaFilter
 */
class AreaFilterTest extends TestCase
{
    public function testGetters(): void
    {
        $filter = new AreaFilter((float) 1, (float) 2);
        $this->assertSame((float) 1, $filter->getBegin());
        $this->assertSame((float) 2, $filter->getEnd());
    }

    /**
     * @dataProvider validDataProvider
     */
    public function testValid(float $begin, float $end): void
    {
        $filter = new AreaFilter($begin, $end);
        $this->assertInstanceOf(AreaFilter::class, $filter);
    }

    public function validDataProvider(): array
    {
        return [
            [1, 2],
            [1.1, 2.2],
            [1.1, 2],
            [1, 2.2],
        ];
    }

    /**
     * @dataProvider invalidDataProvider
     */
    public function testInvalid(float $begin, float $end): void
    {
        $this->expectException(InvalidArgumentException::class);
        new AreaFilter($begin, $end);
    }

    public function invalidDataProvider(): array
    {
        return [
            "negative begin" => [-1, 2],
            "negative end" => [1, -2],
            "end lower than begin" => [2.001, 2],
        ];
    }
}
