<?php

declare(strict_types=1);

namespace Districts\Scraper\Domain\Krakow;

use Districts\Scraper\Domain\CityDTO;
use Districts\Scraper\Domain\CityScraper as CityScraperInterface;
use Districts\Scraper\Domain\DistrictDTO;
use Districts\Scraper\Domain\HtmlFetcher;
use Districts\Scraper\Domain\ProgressReporter;
use Iterator;
use Uri\WhatWg\Url;

final readonly class CityScraper implements CityScraperInterface
{
    public function __construct(
        private HtmlFetcher $htmlFetcher,
        private CityParser $cityParser,
        private DistrictParser $districtParser,
    ) {
    }

    public function getCityName(): string
    {
        return "Kraków";
    }

    public function scrape(?ProgressReporter $progressReporter = null): CityDTO
    {
        $districtUrls = iterator_to_array($this->scrapeDistrictUrls());
        $progressReporter?->setTotal(count($districtUrls));
        $districts = $this->scrapeDistricts($districtUrls, $progressReporter);
        return new CityDTO($this->getCityName(), iterator_to_array($districts));
    }

    /**
     * @param string[] $districtUrls
     *
     * @return Iterator<DistrictDTO>
     */
    private function scrapeDistricts(array $districtUrls, ?ProgressReporter $progressReporter): Iterator
    {
        foreach ($districtUrls as $url) {
            $districtHtml = $this->htmlFetcher->fetchHtml($url);
            yield $this->districtParser->parse($districtHtml);
            $progressReporter?->advance();
        }
    }

    /**
     * @return iterable<string>
     */
    private function scrapeDistrictUrls(): iterable
    {
        $startUrl = new Url("https://www.bip.krakow.pl/?bip_id=1&mmi=453");
        $startHtml = $this->htmlFetcher->fetchHtml($startUrl->toAsciiString());
        foreach ($this->cityParser->extractDistrictUrls($startHtml) as $href) {
            yield $startUrl->resolve($href)->toAsciiString();
        }
    }
}
