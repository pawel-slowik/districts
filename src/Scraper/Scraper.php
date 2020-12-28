<?php

declare(strict_types=1);

namespace Districts\Scraper;

interface Scraper
{
    public function getCityName(): string;

    public function listDistricts(): iterable;
}
