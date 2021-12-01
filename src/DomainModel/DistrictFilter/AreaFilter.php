<?php

declare(strict_types=1);

namespace Districts\DomainModel\DistrictFilter;

use InvalidArgumentException;

class AreaFilter extends Filter
{
    private float $begin;

    private float $end;

    public function __construct(float $begin, float $end)
    {
        if (!self::validate($begin, $end)) {
            throw new InvalidArgumentException();
        }
        $this->begin = $begin;
        $this->end = $end;
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
