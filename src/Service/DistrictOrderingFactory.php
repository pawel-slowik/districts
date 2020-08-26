<?php

declare(strict_types=1);

namespace Service;

use DomainModel\DistrictOrdering;

class DistrictOrderingFactory
{
    public static function createFromRequestInput(?string $column, ?string $direction): DistrictOrdering
    {
        $rules = [
            ["city", "asc", DistrictOrdering::CITY_ASC],
            ["city", "desc", DistrictOrdering::CITY_DESC],
            ["name", "asc", DistrictOrdering::NAME_ASC],
            ["name", "desc", DistrictOrdering::NAME_DESC],
            ["area", "asc", DistrictOrdering::AREA_ASC],
            ["area", "desc", DistrictOrdering::AREA_DESC],
            ["population", "asc", DistrictOrdering::POPULATION_ASC],
            ["population", "desc", DistrictOrdering::POPULATION_DESC],
        ];
        foreach ($rules as $rule) {
            if ([$column, $direction] === [$rule[0], $rule[1]]) {
                return new DistrictOrdering($rule[2]);
            }
        }
        return new DistrictOrdering(DistrictOrdering::DEFAULT);
    }
}
