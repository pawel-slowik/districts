<?php

declare(strict_types=1);

namespace Districts\DomainModel\DistrictFilter;

use InvalidArgumentException;

class CityNameFilter extends Filter
{
    private string $cityName;

    public function __construct(string $cityName)
    {
        if (!self::validate($cityName)) {
            throw new InvalidArgumentException();
        }
        $this->cityName = $cityName;
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
