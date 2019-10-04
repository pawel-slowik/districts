<?php

declare(strict_types=1);

namespace Scraper;

interface DistrictScraper
{
    public function getCityName(): string;

    public function listDistricts(): iterable;
}
