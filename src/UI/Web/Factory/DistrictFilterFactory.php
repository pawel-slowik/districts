<?php

declare(strict_types=1);

namespace Districts\UI\Web\Factory;

use Districts\DomainModel\DistrictFilter\AreaFilter;
use Districts\DomainModel\DistrictFilter\CityNameFilter;
use Districts\DomainModel\DistrictFilter\Filter;
use Districts\DomainModel\DistrictFilter\NameFilter;
use Districts\DomainModel\DistrictFilter\PopulationFilter;

class DistrictFilterFactory
{
    public function createFromRequestInput(?string $column, ?string $value): ?Filter
    {
        if (is_null($value) || (strval($value) === "")) {
            return null;
        }

        switch ($column) {
            case "city":
                return new CityNameFilter($value);
            case "name":
                return new NameFilter($value);
            case "area":
                return new AreaFilter(...array_map("floatval", self::stringToRange($value)));
            case "population":
                return new PopulationFilter(...array_map("intval", self::stringToRange($value)));
        }

        return null;
    }

    private static function stringToRange(string $input): array
    {
        $range = explode("-", $input, 2);
        if (count($range) < 2) {
            $range[1] = $range[0];
        }
        return $range;
    }
}
