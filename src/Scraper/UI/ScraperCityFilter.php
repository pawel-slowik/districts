<?php

declare(strict_types=1);

namespace Districts\Scraper\UI;

use Districts\Scraper\Domain\CityScraper;
use InvalidArgumentException;

class ScraperCityFilter
{
    /**
     * @param string[] $names
     */
    public function __construct(
        private array $names,
    ) {
    }

    /**
     * @param CityScraper[] $scrapers
     *
     * @return iterable<CityScraper>
     */
    public function filter(array $scrapers): iterable
    {
        $supportedNames = $this->getSupportedNames($scrapers);
        foreach ($this->names as $name) {
            if (!in_array($name, $supportedNames, true)) {
                throw new InvalidArgumentException("unsupported city name: {$name}");
            }
        }

        foreach ($scrapers as $scraper) {
            if ($this->matches($scraper->getCityName())) {
                yield $scraper;
            }
        }
    }

    private function matches(string $name): bool
    {
        return empty($this->names) || in_array($name, $this->names, true);
    }

    /**
     * @param CityScraper[] $scrapers
     *
     * @return string[]
     */
    private function getSupportedNames(array $scrapers): array
    {
        return array_map(
            fn ($scraper) => $scraper->getCityName(),
            $scrapers
        );
    }
}
