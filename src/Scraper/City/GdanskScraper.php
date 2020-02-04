<?php

declare(strict_types=1);

namespace Scraper\City;

use Scraper\DistrictScraper;
use Scraper\HtmlFetcher;
use Scraper\HtmlFinder;
use Scraper\RuntimeException;
use Validator\DistrictValidator;
use Laminas\Uri\Uri;

final class GdanskScraper implements DistrictScraper
{
    private $htmlFetcher;

    private $htmlFinder;

    // not injectable
    private $districtBuilder;

    public function __construct(HtmlFetcher $htmlFetcher, HtmlFinder $htmlFinder)
    {
        $this->htmlFetcher = $htmlFetcher;
        $this->htmlFinder = $htmlFinder;
        $this->districtBuilder = new GdanskDistrictBuilder($htmlFinder, new DistrictValidator());
    }

    public function getCityName(): string
    {
        return "Gdańsk";
    }

    public function listDistricts(): iterable
    {
        foreach ($this->listDistrictUrls() as $url) {
            $districtHtml = $this->htmlFetcher->fetchHtml($url);
            yield $this->districtBuilder->buildFromHtml($districtHtml);
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
        $xpath = "//svg/g/polygon[@id]";
        $nodes = $this->htmlFinder->findNodes($html, $xpath);
        if (count($nodes) < 1) {
            throw new RuntimeException();
        }
        foreach ($nodes as $node) {
            $id = $node->getAttribute("id");
            $href = "subpages/dzielnice/html/dzielnice_mapa_alert.php?id={$id}";
            yield Uri::merge($baseUrl, $href)->toString();
        }
    }
}
