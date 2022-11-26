<?php

declare(strict_types=1);

namespace Districts\UI\Web\Factory;

use Districts\Domain\DistrictFilter\AreaFilter;
use Districts\Domain\DistrictFilter\CityNameFilter;
use Districts\Domain\DistrictFilter\Filter;
use Districts\Domain\DistrictFilter\NameFilter;
use Districts\Domain\DistrictFilter\PopulationFilter;

class DistrictFilterFactory
{
    public function createFromRequestInput(?string $column, ?string $value): ?Filter
    {
        if (is_null($value) || (strval($value) === "")) {
            return null;
        }

        return match ($column) {
            "city" => new CityNameFilter($value),
            "name" => new NameFilter($value),
            "area" => new AreaFilter(...array_map("floatval", self::stringToRange($value))),
            "population" => new PopulationFilter(...array_map("intval", self::stringToRange($value))),
            default => null,
        };
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
