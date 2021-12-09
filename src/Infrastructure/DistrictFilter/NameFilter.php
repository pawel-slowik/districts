<?php

declare(strict_types=1);

namespace Districts\Infrastructure\DistrictFilter;

use Districts\DomainModel\DistrictFilter\NameFilter as DomainNameFilter;

class NameFilter extends Filter
{
    private string $name;

    public function __construct(DomainNameFilter $domainFilter)
    {
        $this->name = $domainFilter->getName();
    }

    public function where(): string
    {
        return "d.name.name LIKE :search";
    }

    public function parameters(): array
    {
        return [
            "search" => $this->dqlLike($this->name),
        ];
    }
}
