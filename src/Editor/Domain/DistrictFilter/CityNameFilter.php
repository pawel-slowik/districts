<?php

declare(strict_types=1);

namespace Districts\Editor\Domain\DistrictFilter;

use InvalidArgumentException;

readonly class CityNameFilter extends Filter
{
    public function __construct(
        public string $cityName,
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
