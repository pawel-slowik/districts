<?php

declare(strict_types=1);

namespace Districts\DomainModel\Scraper\Krakow;

use Districts\DomainModel\Scraper\CityDTO;
use Districts\DomainModel\Scraper\CityScraper as CityScraperInterface;
use Districts\DomainModel\Scraper\HtmlFetcher;
use Districts\DomainModel\Scraper\HtmlFinder;
use Districts\DomainModel\Scraper\RuntimeException;
use Laminas\Uri\Uri;

final class CityScraper implements CityScraperInterface
{
    private $htmlFetcher;

    private $htmlFinder;

    // not injectable
    private $districtScraper;

    public function __construct(HtmlFetcher $htmlFetcher, HtmlFinder $htmlFinder)
    {
        $this->htmlFetcher = $htmlFetcher;
        $this->htmlFinder = $htmlFinder;
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
        return $this->extractDistrictUrls($startHtml, $startUrl);
    }

    private function extractDistrictUrls(string $html, string $baseUrl): iterable
    {
        $xpath = "//map[@name='wyb']/area[@href]";
        $nodes = $this->htmlFinder->findNodes($html, $xpath);
        if (count($nodes) < 1) {
            throw new RuntimeException();
        }
        foreach ($nodes as $node) {
            $href = $node->getAttribute("href");
            yield Uri::merge($baseUrl, $href)->toString();
        }
    }
}
