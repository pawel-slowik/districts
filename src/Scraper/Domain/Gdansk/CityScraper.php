<?php

declare(strict_types=1);

namespace Districts\Scraper\Domain\Gdansk;

use Districts\Scraper\Domain\CityDTO;
use Districts\Scraper\Domain\CityScraper as CityScraperInterface;
use Districts\Scraper\Domain\HtmlFetcher;
use Iterator;
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
        return "Gdańsk";
    }

    public function scrape(): CityDTO
    {
        return new CityDTO($this->getCityName(), iterator_to_array($this->listDistricts()));
    }

    private function listDistricts(): Iterator
    {
        foreach ($this->listDistrictUrls() as $url) {
            $districtHtml = $this->htmlFetcher->fetchHtml($url);
            yield $this->districtParser->parse($districtHtml);
        }
    }

    private function listDistrictUrls(): iterable
    {
        $startUrl = "https://www.gdansk.pl/dzielnice";
        $startHtml = $this->htmlFetcher->fetchHtml($startUrl);
        foreach ($this->cityParser->extractDistrictUrls($startHtml) as $href) {
            yield Uri::merge($startUrl, $href)->toString();
        }
    }
}
