<?php

declare(strict_types=1);

namespace Districts\DomainModel\Scraper\Gdansk;

use Districts\DomainModel\Scraper\HtmlFinder;
use Districts\DomainModel\Scraper\RuntimeException;

class CityParser
{
    private $htmlFinder;

    public function __construct(HtmlFinder $htmlFinder)
    {
        $this->htmlFinder = $htmlFinder;
    }

    public function extractDistrictUrls(string $html): iterable
    {
        $xpath = "//polygon[@id]";
        $nodes = $this->htmlFinder->findNodes($html, $xpath);
        if (count($nodes) < 1) {
            throw new RuntimeException();
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
            throw new RuntimeException();
        }
        return $match[1];
    }
}
