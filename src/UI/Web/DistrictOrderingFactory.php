<?php

declare(strict_types=1);

namespace UI\Web;

use DomainModel\DistrictOrdering;

class DistrictOrderingFactory
{
    public static function createFromRequestInput(?string $column, ?string $direction): DistrictOrdering
    {
        $rules = [
            ["city", "asc", DistrictOrdering::CITY_NAME, DistrictOrdering::ASC],
            ["city", "desc", DistrictOrdering::CITY_NAME, DistrictOrdering::DESC],
            ["name", "asc", DistrictOrdering::DISTRICT_NAME, DistrictOrdering::ASC],
            ["name", "desc", DistrictOrdering::DISTRICT_NAME, DistrictOrdering::DESC],
            ["area", "asc", DistrictOrdering::AREA, DistrictOrdering::ASC],
            ["area", "desc", DistrictOrdering::AREA, DistrictOrdering::DESC],
            ["population", "asc", DistrictOrdering::POPULATION, DistrictOrdering::ASC],
            ["population", "desc", DistrictOrdering::POPULATION, DistrictOrdering::DESC],
        ];
        foreach ($rules as $rule) {
            if ([$column, $direction] === [$rule[0], $rule[1]]) {
                return new DistrictOrdering($rule[2], $rule[3]);
            }
        }
        return new DistrictOrdering(DistrictOrdering::FULL_NAME, DistrictOrdering::ASC);
    }
}
