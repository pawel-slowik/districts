<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\Domain\DistrictFilter;

use Districts\Editor\Domain\DistrictFilter\AreaFilter;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Editor\Domain\DistrictFilter\AreaFilter
 */
class AreaFilterTest extends TestCase
{
    public function testProperties(): void
    {
        $filter = new AreaFilter((float) 1, (float) 2);
        $this->assertSame((float) 1, $filter->begin);
        $this->assertSame((float) 2, $filter->end);
    }

    /**
     * @dataProvider validDataProvider
     */
    public function testValid(float $begin, float $end): void
    {
        $filter = new AreaFilter($begin, $end);
        $this->assertInstanceOf(AreaFilter::class, $filter);
    }

    /**
     * @return array<array{0: float, 1: float}>
     */
    public static function validDataProvider(): array
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

    /**
     * @return array<string, array{0: float, 1: float}>
     */
    public static function invalidDataProvider(): array
    {
        return [
            "negative begin" => [-1, 2],
            "negative end" => [1, -2],
            "end lower than begin" => [2.001, 2],
        ];
    }
}
