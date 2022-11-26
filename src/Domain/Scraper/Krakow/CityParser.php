<?php

declare(strict_types=1);

namespace Districts\Domain\Scraper\Krakow;

use Districts\Domain\Scraper\Exception\InvalidHtmlException;
use Districts\Domain\Scraper\Exception\ParsingException;
use Districts\Domain\Scraper\HtmlFinder;

class CityParser
{
    public function __construct(
        private HtmlFinder $htmlFinder,
    ) {
    }

    public function extractDistrictUrls(string $html): iterable
    {
        // phpcs:ignore Generic.Files.LineLength.TooLong
        $xpath = "//a[contains(@class, 'nav-link') and .='DZIELNICE']/following::ul/li/a[@class='nav-link' and starts-with(., 'Dzielnica ')]";
        try {
            $nodes = $this->htmlFinder->findNodes($html, $xpath);
        } catch (InvalidHtmlException $exception) {
            throw new ParsingException();
        }
        if (count($nodes) < 1) {
            throw new ParsingException();
        }
        foreach ($nodes as $node) {
            yield $node->getAttribute("href");
        }
    }
}
