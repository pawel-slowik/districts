<?php

declare(strict_types=1);

namespace Districts\Scraper\Domain\Krakow;

use Districts\Scraper\Domain\CityDTO;
use Districts\Scraper\Domain\CityScraper as CityScraperInterface;
use Districts\Scraper\Domain\DistrictDTO;
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
        return "Kraków";
    }

    public function scrape(): CityDTO
    {
        return new CityDTO($this->getCityName(), iterator_to_array($this->listDistricts()));
    }

    /**
     * @return Iterator<DistrictDTO>
     */
    private function listDistricts(): Iterator
    {
        foreach ($this->listDistrictUrls() as $url) {
            $districtHtml = $this->htmlFetcher->fetchHtml($url);
            yield $this->districtParser->parse($districtHtml);
        }
    }

    /**
     * @return iterable<string>
     */
    private function listDistrictUrls(): iterable
    {
        $startUrl = "https://www.bip.krakow.pl/?bip_id=1&mmi=453";
        $startHtml = $this->htmlFetcher->fetchHtml($startUrl);
        foreach ($this->cityParser->extractDistrictUrls($startHtml) as $href) {
            yield Uri::merge($startUrl, $href)->toString();
        }
    }
}
