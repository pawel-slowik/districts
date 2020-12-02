<?php

declare(strict_types=1);

namespace Districts\Scraper\District;

interface Scraper
{
    public function getCityName(): string;

    public function listDistricts(): iterable;
}
