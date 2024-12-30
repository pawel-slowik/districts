<?php

declare(strict_types=1);

namespace Districts\Editor\Domain;

enum DistrictOrderingField
{
    case FullName;
    case CityName;
    case DistrictName;
    case Area;
    case Population;
}
