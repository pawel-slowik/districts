<?php

declare(strict_types=1);

namespace Districts\Editor\Infrastructure\DistrictFilter;

use Districts\Editor\Domain\DistrictFilter\AreaFilter as DomainAreaFilter;
use Districts\Editor\Domain\DistrictFilter\CityNameFilter as DomainCityNameFilter;
use Districts\Editor\Domain\DistrictFilter\Filter as DomainFilter;
use Districts\Editor\Domain\DistrictFilter\NameFilter as DomainNameFilter;
use Districts\Editor\Domain\DistrictFilter\PopulationFilter as DomainPopulationFilter;
use InvalidArgumentException;

class FilterFactory
{
    public function fromDomainFilter(?DomainFilter $domainFilter): ?Filter
    {
        if (!$domainFilter) {
            return null;
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
