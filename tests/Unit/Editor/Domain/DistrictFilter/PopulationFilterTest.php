<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\Domain\DistrictFilter;

use Districts\Editor\Domain\DistrictFilter\PopulationFilter;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(PopulationFilter::class)]
final class PopulationFilterTest extends TestCase
{
    public function testProperties(): void
    {
        $filter = new PopulationFilter(1, 2);
        $this->assertSame(1, $filter->begin);
        $this->assertSame(2, $filter->end);
    }

    #[DataProvider('invalidDataProvider')]
    public function testInvalid(int $begin, int $end): void
    {
        $this->expectException(InvalidArgumentException::class);
        new PopulationFilter($begin, $end);
    }

    /**
     * @return array<string, array{0: int, 1: int}>
     */
    public static function invalidDataProvider(): array
    {
        return [
            "negative begin" => [-1, 2],
            "negative end" => [1, -2],
            "end lower than begin" => [3, 2],
        ];
    }
}
