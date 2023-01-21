<?php

declare(strict_types=1);

namespace Districts\Editor\Domain\DistrictFilter;

use InvalidArgumentException;

class CityNameFilter extends Filter
{
    public function __construct(
        public readonly string $cityName,
    ) {
        if (!self::validate($cityName)) {
            throw new InvalidArgumentException();
        }
    }

    private static function validate(string $cityName): bool
    {
        return $cityName !== "";
    }
}
