<?php

declare(strict_types=1);

namespace Districts\Domain\DistrictFilter;

use InvalidArgumentException;

class PopulationFilter extends Filter
{
    public function __construct(
        private int $begin,
        private int $end,
    ) {
        if (!self::validate($begin, $end)) {
            throw new InvalidArgumentException();
        }
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
