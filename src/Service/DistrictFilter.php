<?php

declare(strict_types=1);

namespace Service;

class DistrictFilter
{
    private const PARSERS = [
        "city" => [
            "type" => DistrictService::FILTER_CITY,
            "callback" => "strval",
        ],
        "name" => [
            "type" => DistrictService::FILTER_NAME,
            "callback" => "strval",
        ],
        "area" => [
            "type" => DistrictService::FILTER_AREA,
            "callback" => [self::class, "stringToRange"],
        ],
        "population" => [
            "type" => DistrictService::FILTER_POPULATION,
            "callback" => [self::class, "stringToRange"],
        ],
    ];

    private $type;

    private $value;

    public function __construct(int $type, $value)
    {
        $this->type = $type;
        $this->value = $value;
    }

    public static function createFromRequestInput(?string $column, ?string $value): ?self
    {
        if (is_null($value) || (strval($value) === "")) {
            return null;
        }
        if (is_null($column) || !array_key_exists($column, self::PARSERS)) {
            return null;
        }
        return new self(
            self::PARSERS[$column]["type"],
            self::PARSERS[$column]["callback"]($value),
        );
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getValue()
    {
        return $this->value;
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
