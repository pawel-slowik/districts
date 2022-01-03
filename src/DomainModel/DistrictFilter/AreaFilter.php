<?php

declare(strict_types=1);

namespace Districts\DomainModel\DistrictFilter;

use InvalidArgumentException;

class AreaFilter extends Filter
{
    public function __construct(
        private float $begin,
        private float $end,
    ) {
        if (!self::validate($begin, $end)) {
            throw new InvalidArgumentException();
        }
    }

    public function getBegin(): float
    {
        return $this->begin;
    }

    public function getEnd(): float
    {
        return $this->end;
    }

    private static function validate(float $begin, float $end): bool
    {
        return $begin >= 0 && $end >= 0 && $end >= $begin;
    }
}
