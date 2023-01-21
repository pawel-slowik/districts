<?php

declare(strict_types=1);

namespace Districts\Editor\Domain\DistrictFilter;

use InvalidArgumentException;

class AreaFilter extends Filter
{
    public function __construct(
        public readonly float $begin,
        public readonly float $end,
    ) {
        if (!self::validate($begin, $end)) {
            throw new InvalidArgumentException();
        }
    }

    private static function validate(float $begin, float $end): bool
    {
        return $begin >= 0 && $end >= 0 && $end >= $begin;
    }
}
