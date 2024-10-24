<?php

declare(strict_types=1);

namespace Districts\Scraper\Domain\Krakow;

use Districts\Scraper\Domain\CityDTO;
use Districts\Scraper\Domain\CityScraper as CityScraperInterface;
use Districts\Scraper\Domain\DistrictDTO;
use Districts\Scraper\Domain\HtmlFetcher;
use Districts\Scraper\Domain\ProgressReporter;
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

    public function scrape(?ProgressReporter $progressReporter = null): CityDTO
    {
        $districtUrls = iterator_to_array($this->scrapeDistrictUrls());
        if ($progressReporter) {
            $progressReporter->setTotal(count($districtUrls));
        }
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
            if ($progressReporter) {
                $progressReporter->advance();
            }
        }
    }

    /**
     * @return iterable<string>
     */
    private function scrapeDistrictUrls(): iterable
    {
        $startUrl = "https://www.bip.krakow.pl/?bip_id=1&mmi=453";
        $startHtml = $this->htmlFetcher->fetchHtml($startUrl);
        foreach ($this->cityParser->extractDistrictUrls($startHtml) as $href) {
            yield Uri::merge($startUrl, $href)->toString();
        }
    }
}
