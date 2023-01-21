<?php

declare(strict_types=1);

namespace Districts\Editor\Infrastructure\DistrictFilter;

class NullFilter extends Filter
{
    public function where(): string
    {
        return "";
    }

    public function parameters(): array
    {
        return [];
    }
}
