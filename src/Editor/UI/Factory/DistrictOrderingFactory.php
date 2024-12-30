<?php

declare(strict_types=1);

namespace Districts\Editor\UI\Factory;

use Districts\Editor\Domain\DistrictOrdering;
use Districts\Editor\Domain\DistrictOrderingField;
use Districts\Editor\Domain\OrderingDirection;

class DistrictOrderingFactory
{
    public function createFromRequestInput(?string $column, ?string $direction): DistrictOrdering
    {
        $rules = [
            ["city", "asc", DistrictOrderingField::CityName, OrderingDirection::Asc],
            ["city", "desc", DistrictOrderingField::CityName, OrderingDirection::Desc],
            ["name", "asc", DistrictOrderingField::DistrictName, OrderingDirection::Asc],
            ["name", "desc", DistrictOrderingField::DistrictName, OrderingDirection::Desc],
            ["area", "asc", DistrictOrderingField::Area, OrderingDirection::Asc],
            ["area", "desc", DistrictOrderingField::Area, OrderingDirection::Desc],
            ["population", "asc", DistrictOrderingField::Population, OrderingDirection::Asc],
            ["population", "desc", DistrictOrderingField::Population, OrderingDirection::Desc],
        ];
        foreach ($rules as $rule) {
            if ([$column, $direction] === [$rule[0], $rule[1]]) {
                return new DistrictOrdering($rule[2], $rule[3]);
            }
        }
        return new DistrictOrdering(DistrictOrderingField::FullName, OrderingDirection::Asc);
    }
}
