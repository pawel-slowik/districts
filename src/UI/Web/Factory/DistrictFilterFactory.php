<?php

declare(strict_types=1);

namespace Districts\UI\Web\Factory;

use Districts\DomainModel\DistrictFilter;

class DistrictFilterFactory
{
    public function createFromRequestInput(?string $column, ?string $value): ?DistrictFilter
    {
        if (is_null($value) || (strval($value) === "")) {
            return null;
        }

        switch ($column) {
            case "city":
                return new DistrictFilter(DistrictFilter::TYPE_CITY, $value);
            case "name":
                return new DistrictFilter(DistrictFilter::TYPE_NAME, $value);
            case "area":
                return new DistrictFilter(DistrictFilter::TYPE_AREA, self::stringToRange($value));
            case "population":
                return new DistrictFilter(DistrictFilter::TYPE_POPULATION, self::stringToRange($value));
        }

        return null;
    }

    private static function stringToRange(string $input): array
    {
        $range = array_map("floatval", explode("-", $input, 2));
        if (count($range) < 2) {
            $range[1] = $range[0];
        }
        return $range;
    }
}
