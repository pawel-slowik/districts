<?php

declare(strict_types=1);

namespace Districts\DomainModel\Scraper\Krakow;

use Districts\DomainModel\Scraper\CityDTO;
use Districts\DomainModel\Scraper\CityScraper as CityScraperInterface;
use Districts\DomainModel\Scraper\HtmlFetcher;
use Districts\DomainModel\Scraper\HtmlFinder;
use Laminas\Uri\Uri;

final class CityScraper implements CityScraperInterface
{
    private $htmlFetcher;

    // not injectable
    private $cityParser;

    // not injectable
    private $districtScraper;

    public function __construct(HtmlFetcher $htmlFetcher, HtmlFinder $htmlFinder)
    {
        $this->htmlFetcher = $htmlFetcher;
        $this->cityParser = new CityParser($htmlFinder);
        $this->districtScraper = new DistrictScraper($htmlFinder);
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
            yield $this->districtScraper->scrape($districtHtml);
        }
    }

    private function listDistrictUrls(): iterable
    {
        $startUrl = "http://appimeri.um.krakow.pl/app-pub-dzl/pages/DzlViewAll.jsf?a=1&lay=normal&fo=0";
        $startHtml = $this->htmlFetcher->fetchHtml($startUrl);
        foreach ($this->cityParser->extractDistrictUrls($startHtml) as $href) {
            yield Uri::merge($startUrl, $href)->toString();
        }
    }
}
