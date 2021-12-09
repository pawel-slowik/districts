<?php

declare(strict_types=1);

namespace Districts\Infrastructure\DistrictFilter;

use Districts\DomainModel\DistrictFilter\AreaFilter as DomainAreaFilter;
use Districts\DomainModel\DistrictFilter\CityNameFilter as DomainCityNameFilter;
use Districts\DomainModel\DistrictFilter\Filter as DomainFilter;
use Districts\DomainModel\DistrictFilter\NameFilter as DomainNameFilter;
use Districts\DomainModel\DistrictFilter\PopulationFilter as DomainPopulationFilter;
use InvalidArgumentException;

class FilterFactory
{
    public function fromDomainFilter(?DomainFilter $domainFilter): Filter
    {
        if (!$domainFilter) {
            return new NullFilter();
        }

        if ($domainFilter instanceof DomainCityNameFilter) {
            return new CityNameFilter($domainFilter);
        }

        if ($domainFilter instanceof DomainNameFilter) {
            return new NameFilter($domainFilter);
        }

        if ($domainFilter instanceof DomainAreaFilter) {
            return new AreaFilter($domainFilter);
        }

        if ($domainFilter instanceof DomainPopulationFilter) {
            return new PopulationFilter($domainFilter);
        }

        throw new InvalidArgumentException();
    }
}
