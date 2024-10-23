<?php

declare(strict_types=1);

namespace Districts\Scraper\Domain\Krakow;

use Districts\Scraper\Domain\Exception\InvalidHtmlException;
use Districts\Scraper\Domain\Exception\ParsingException;
use Districts\Scraper\Domain\HtmlFinder;

class CityParser
{
    public function __construct(
        private HtmlFinder $htmlFinder,
    ) {
    }

    /**
     * @return iterable<string>
     */
    public function extractDistrictUrls(string $html): iterable
    {
        // phpcs:ignore Generic.Files.LineLength.TooLong
        $xpath = "//a[contains(@class, 'nav-link') and .='Dzielnice']/following::ul/li/a[@class='nav-link' and starts-with(., 'Dzielnica ')]";
        try {
            $nodes = $this->htmlFinder->findNodes($html, $xpath);
        } catch (InvalidHtmlException) {
            throw new ParsingException();
        }
        if (count($nodes) < 1) {
            throw new ParsingException();
        }
        foreach ($nodes as $node) {
            yield $this->htmlFinder->getAttribute($node, "href");
        }
    }
}
