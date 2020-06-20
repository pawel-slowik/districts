<?php

declare(strict_types=1);

namespace Scraper\District;

interface Scraper
{
    public function getCityName(): string;

    public function listDistricts(): iterable;
}
