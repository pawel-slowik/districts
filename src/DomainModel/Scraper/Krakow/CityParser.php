<?php

declare(strict_types=1);

namespace Districts\DomainModel\Scraper\Krakow;

use Districts\DomainModel\Exception\InvalidHtmlException;
use Districts\DomainModel\Exception\ParsingException;
use Districts\DomainModel\Scraper\HtmlFinder;

class CityParser
{
    private $htmlFinder;

    public function __construct(HtmlFinder $htmlFinder)
    {
        $this->htmlFinder = $htmlFinder;
    }

    public function extractDistrictUrls(string $html): iterable
    {
        $xpath = "//map[@name='wyb']/area[@href]";
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
