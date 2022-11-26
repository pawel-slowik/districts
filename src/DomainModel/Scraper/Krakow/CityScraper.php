<?php

declare(strict_types=1);

namespace Districts\DomainModel\Scraper\Krakow;

use Districts\DomainModel\Scraper\CityDTO;
use Districts\DomainModel\Scraper\CityScraper as CityScraperInterface;
use Districts\DomainModel\Scraper\HtmlFetcher;
use Laminas\Uri\Uri;

final class CityScraper implements CityScraperInterface
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

    public function scrape(): CityDTO
    {
        return new CityDTO($this->getCityName(), $this->listDistricts());
    }

    private function listDistricts(): iterable
    {
        foreach ($this->listDistrictUrls() as $url) {
            $districtHtml = $this->htmlFetcher->fetchHtml($url);
            yield $this->districtParser->parse($districtHtml);
        }
    }

    private function listDistrictUrls(): iterable
    {
        $startUrl = "https://www.bip.krakow.pl/?bip_id=1&mmi=453";
        $startHtml = $this->htmlFetcher->fetchHtml($startUrl);
        foreach ($this->cityParser->extractDistrictUrls($startHtml) as $href) {
            yield Uri::merge($startUrl, $href)->toString();
        }
    }
}
