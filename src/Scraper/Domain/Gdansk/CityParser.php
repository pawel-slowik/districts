<?php

declare(strict_types=1);

namespace Districts\Scraper\Domain\Gdansk;

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
        $xpath = "//polygon[@id]";
        try {
            $nodes = $this->htmlFinder->findNodes($html, $xpath);
        } catch (InvalidHtmlException) {
            throw new ParsingException();
        }
        if (count($nodes) < 1) {
            throw new ParsingException();
        }
        foreach ($nodes as $node) {
            $id = $this->fixPolygonId($this->htmlFinder->getAttribute($node, "id"));
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
