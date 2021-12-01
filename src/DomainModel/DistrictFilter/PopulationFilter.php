<?php

declare(strict_types=1);

namespace Districts\DomainModel\DistrictFilter;

use InvalidArgumentException;

class PopulationFilter extends Filter
{
    private int $begin;

    private int $end;

    public function __construct(int $begin, int $end)
    {
        if (!self::validate($begin, $end)) {
            throw new InvalidArgumentException();
        }
        $this->begin = $begin;
        $this->end = $end;
    }

    public function getBegin(): int
    {
        return $this->begin;
    }

    public function getEnd(): int
    {
        return $this->end;
    }

    private static function validate(int $begin, int $end): bool
    {
        return $begin >= 0 && $end >= 0 && $end >= $begin;
    }
}
