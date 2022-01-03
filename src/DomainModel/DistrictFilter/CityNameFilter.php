<?php

declare(strict_types=1);

namespace Districts\DomainModel\DistrictFilter;

use InvalidArgumentException;

class CityNameFilter extends Filter
{
    public function __construct(
        private string $cityName,
    ) {
        if (!self::validate($cityName)) {
            throw new InvalidArgumentException();
        }
    }

    public function getCityName(): string
    {
        return $this->cityName;
    }

    private static function validate(string $cityName): bool
    {
        return $cityName !== "";
    }
}
