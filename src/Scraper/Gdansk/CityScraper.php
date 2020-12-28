<?php

declare(strict_types=1);

namespace Districts\Scraper\Gdansk;

use Districts\Scraper\CityDTO;
use Districts\Scraper\CityScraper as ScraperInterface;
use Districts\Scraper\HtmlFetcher;
use Districts\Scraper\HtmlFinder;
use Districts\Scraper\RuntimeException;
use Laminas\Uri\Uri;

final class CityScraper implements ScraperInterface
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
        return "Gdańsk";
    }

    public function scrape(): CityDTO
    {
        return new CityDTO($this->getCityName(), $this->listDistricts());
    }

    private function listDistricts(): iterable
    {
        foreach ($this->listDistrictUrls() as $url) {
            $districtHtml = $this->htmlFetcher->fetchHtml($url);
            yield $this->districtScraper->buildFromHtml($districtHtml);
        }
    }

    private function listDistrictUrls(): iterable
    {
        $startUrl = "https://www.gdansk.pl/dzielnice";
        $startHtml = $this->htmlFetcher->fetchHtml($startUrl);
        return $this->extractDistrictUrls($startHtml, $startUrl);
    }

    private function extractDistrictUrls(string $html, string $baseUrl): iterable
    {
        $xpath = "//polygon[@id]";
        $nodes = $this->htmlFinder->findNodes($html, $xpath);
        if (count($nodes) < 1) {
            throw new RuntimeException();
        }
        foreach ($nodes as $node) {
            $id = $this->fixPolygonId($node->getAttribute("id"));
            $href = "subpages/dzielnice/html/dzielnice_mapa_alert.php?id={$id}";
            yield Uri::merge($baseUrl, $href)->toString();
        }
    }

    private function fixPolygonId(string $id): string
    {
        $match = [];
        if (!preg_match("/^([0-9]+)/", $id, $match)) {
            throw new RuntimeException();
        }
        return $match[1];
    }
}
