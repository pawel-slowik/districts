<?php

declare(strict_types=1);

namespace Service;

use DomainModel\DistrictFilter;

class DistrictFilterFactory
{
    private const PARSERS = [
        "city" => [
            "type" => DistrictFilter::TYPE_CITY,
            "callback" => "strval",
        ],
        "name" => [
            "type" => DistrictFilter::TYPE_NAME,
            "callback" => "strval",
        ],
        "area" => [
            "type" => DistrictFilter::TYPE_AREA,
            "callback" => [self::class, "stringToRange"],
        ],
        "population" => [
            "type" => DistrictFilter::TYPE_POPULATION,
            "callback" => [self::class, "stringToRange"],
        ],
    ];

    public static function createFromRequestInput(?string $column, ?string $value): ?DistrictFilter
    {
        if (is_null($value) || (strval($value) === "")) {
            return null;
        }
        if (is_null($column) || !array_key_exists($column, self::PARSERS)) {
            return null;
        }
        return new DistrictFilter(
            self::PARSERS[$column]["type"],
            self::PARSERS[$column]["callback"]($value),
        );
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
