<?php

declare(strict_types=1);

namespace Districts\Scraper\Domain;

interface CityScraper
{
    public function getCityName(): string;

    public function scrape(?ProgressReporter $progressReporter = null): CityDTO;
}
