<?php

declare(strict_types=1);

namespace Districts\Editor\Infrastructure\Doctrine;

use Districts\Editor\Domain\DistrictFilter\AreaFilter;
use Districts\Editor\Domain\DistrictFilter\CityNameFilter;
use Districts\Editor\Domain\DistrictFilter\Filter as DomainFilter;
use Districts\Editor\Domain\DistrictFilter\NameFilter;
use Districts\Editor\Domain\DistrictFilter\PopulationFilter;
use InvalidArgumentException;

final readonly class DistrictFilter extends Filter
{
    public static function fromDomainFilter(DomainFilter $domainFilter): self
    {
        if ($domainFilter instanceof CityNameFilter) {
            return new self(
                "c.name LIKE :search",
                [
                    "search" => self::dqlLike($domainFilter->cityName),
                ],
            );
        }

        if ($domainFilter instanceof NameFilter) {
            return new self(
                "d.name.name LIKE :search",
                [
                    "search" => self::dqlLike($domainFilter->name),
                ],
            );
        }

        if ($domainFilter instanceof AreaFilter) {
            return new self(
                "d.area.area >= :low AND d.area.area <= :high",
                [
                    "low" => $domainFilter->begin,
                    "high" => $domainFilter->end,
                ],
            );
        }

        if ($domainFilter instanceof PopulationFilter) {
            return new self(
                "d.population.population >= :low AND d.population.population <= :high",
                [
                    "low" => $domainFilter->begin,
                    "high" => $domainFilter->end,
                ],
            );
        }

        throw new InvalidArgumentException();
    }
}
