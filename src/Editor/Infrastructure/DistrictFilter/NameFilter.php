<?php

declare(strict_types=1);

namespace Districts\Editor\Infrastructure\DistrictFilter;

use Districts\Editor\Domain\DistrictFilter\NameFilter as DomainNameFilter;

class NameFilter extends Filter
{
    private string $name;

    public function __construct(DomainNameFilter $domainFilter)
    {
        $this->name = $domainFilter->name;
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
