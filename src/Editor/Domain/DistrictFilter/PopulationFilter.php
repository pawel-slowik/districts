<?php

declare(strict_types=1);

namespace Districts\Editor\Domain\DistrictFilter;

use InvalidArgumentException;

readonly class PopulationFilter extends Filter
{
    public function __construct(
        public int $begin,
        public int $end,
    ) {
        if (!self::validate($begin, $end)) {
            throw new InvalidArgumentException();
        }
    }

    private static function validate(int $begin, int $end): bool
    {
        return $begin >= 0 && $end >= 0 && $end >= $begin;
    }
}
