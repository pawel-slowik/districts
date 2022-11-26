<?php

declare(strict_types=1);

namespace Districts\Domain\Scraper;

interface CityScraper
{
    public function getCityName(): string;

    public function scrape(): CityDTO;
}
