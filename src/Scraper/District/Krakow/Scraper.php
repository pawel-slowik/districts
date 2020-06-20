<?php

declare(strict_types=1);

namespace Scraper\District\Krakow;

use Scraper\District\Scraper as ScraperInterface;
use Scraper\HtmlFetcher;
use Scraper\HtmlFinder;
use Scraper\RuntimeException;
use Validator\DistrictValidator;
use Laminas\Uri\Uri;

final class Scraper implements ScraperInterface
{
    private $htmlFetcher;

    private $htmlFinder;

    // not injectable
    private $districtBuilder;

    public function __construct(HtmlFetcher $htmlFetcher, HtmlFinder $htmlFinder)
    {
        $this->htmlFetcher = $htmlFetcher;
        $this->htmlFinder = $htmlFinder;
        $this->districtBuilder = new Builder($htmlFinder, new DistrictValidator());
    }

    public function getCityName(): string
    {
        return "Kraków";
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
