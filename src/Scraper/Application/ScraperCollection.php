<?php

declare(strict_types=1);

namespace Districts\Scraper\Application;

use Districts\Scraper\Domain\CityScraper;
use InvalidArgumentException;

readonly class ScraperCollection
{
    /**
     * @var CityScraper[]
     */
    private array $scrapers;

    public function __construct(CityScraper ...$scrapers)
    {
        $this->scrapers = $scrapers;
    }

    /**
     * @param string[] $names
     *
     * @return CityScraper[]
     */
    public function filterByCityNames(array $names): array
    {
        if ($names === []) {
            return $this->scrapers;
        }

        $supportedNames = array_map(
            static fn ($scraper) => $scraper->getCityName(),
            $this->scrapers,
        );
        foreach ($names as $name) {
            if (!in_array($name, $supportedNames, true)) {
                throw new InvalidArgumentException("unsupported city name: {$name}");
            }
        }

        return array_values(array_filter(
            $this->scrapers,
            static fn ($scraper) => in_array($scraper->getCityName(), $names, true),
        ));
    }
}
