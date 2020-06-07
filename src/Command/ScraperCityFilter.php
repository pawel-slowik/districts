<?php

declare(strict_types=1);

namespace Command;

class ScraperCityFilter
{
    private $names;

    public function __construct(array $scrapers, array $names)
    {
        $supportedNames = $this->getSupportedNames($scrapers);
        foreach ($names as $name) {
            if (!in_array($name, $supportedNames, true)) {
                throw new \InvalidArgumentException("unsupported city name: {$name}");
            }
        }
        $this->names = $names;
    }

    public function filter(iterable $scrapers): iterable
    {
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
