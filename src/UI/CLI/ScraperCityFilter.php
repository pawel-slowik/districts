<?php

declare(strict_types=1);

namespace Districts\UI\CLI;

use InvalidArgumentException;

class ScraperCityFilter
{
    private array $names;

    public function __construct(array $names)
    {
        $this->names = $names;
    }

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

    private function getSupportedNames(array $scrapers): array
    {
        return array_map(
            function ($scraper) {
                return $scraper->getCityName();
            },
            $scrapers
        );
    }
}
