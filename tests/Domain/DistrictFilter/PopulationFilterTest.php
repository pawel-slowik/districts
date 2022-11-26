<?php

declare(strict_types=1);

namespace Districts\Test\Domain\DistrictFilter;

use Districts\Domain\DistrictFilter\PopulationFilter;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Domain\DistrictFilter\PopulationFilter
 */
class PopulationFilterTest extends TestCase
{
    public function testGetters(): void
    {
        $filter = new PopulationFilter(1, 2);
        $this->assertSame(1, $filter->getBegin());
        $this->assertSame(2, $filter->getEnd());
    }

    /**
     * @dataProvider invalidDataProvider
     */
    public function testInvalid(int $begin, int $end): void
    {
        $this->expectException(InvalidArgumentException::class);
        new PopulationFilter($begin, $end);
    }

    public function invalidDataProvider(): array
    {
        return [
            "negative begin" => [-1, 2],
            "negative end" => [1, -2],
            "end lower than begin" => [3, 2],
        ];
    }
}
