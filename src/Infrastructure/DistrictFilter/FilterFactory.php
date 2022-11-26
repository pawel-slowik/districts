<?php

declare(strict_types=1);

namespace Districts\Infrastructure\DistrictFilter;

use Districts\Domain\DistrictFilter\AreaFilter as DomainAreaFilter;
use Districts\Domain\DistrictFilter\CityNameFilter as DomainCityNameFilter;
use Districts\Domain\DistrictFilter\Filter as DomainFilter;
use Districts\Domain\DistrictFilter\NameFilter as DomainNameFilter;
use Districts\Domain\DistrictFilter\PopulationFilter as DomainPopulationFilter;
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
