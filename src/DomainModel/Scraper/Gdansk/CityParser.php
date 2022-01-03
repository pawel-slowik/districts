<?php

declare(strict_types=1);

namespace Districts\DomainModel\Scraper\Gdansk;

use Districts\DomainModel\Scraper\Exception\InvalidHtmlException;
use Districts\DomainModel\Scraper\Exception\ParsingException;
use Districts\DomainModel\Scraper\HtmlFinder;

class CityParser
{
    public function __construct(
        private HtmlFinder $htmlFinder,
    ) {
    }

    public function extractDistrictUrls(string $html): iterable
    {
        $xpath = "//polygon[@id]";
        try {
            $nodes = $this->htmlFinder->findNodes($html, $xpath);
        } catch (InvalidHtmlException $exception) {
            throw new ParsingException();
        }
        if (count($nodes) < 1) {
            throw new ParsingException();
        }
        foreach ($nodes as $node) {
            $id = $this->fixPolygonId($node->getAttribute("id"));
            yield "subpages/dzielnice/html/4-dzielnice_mapa_alert.php?id={$id}";
        }
    }

    private function fixPolygonId(string $id): string
    {
        $match = [];
        if (!preg_match("/^([0-9]+)/", $id, $match)) {
            throw new ParsingException();
        }
        return $match[1];
    }
}
