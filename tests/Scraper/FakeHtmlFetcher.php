<?php

declare(strict_types=1);

namespace Test\Scraper;

use Scraper\HtmlFetcher;

class FakeHtmlFetcher implements HtmlFetcher
{
    protected $data;

    public function __construct()
    {
        $urlFileMap = [];

        // Note: loads a single test input file for all districts. This is OK
        // because Scrapers don't have any knowledge about district properties
        // (only Builders do).

        $urlFileMap["https://www.gdansk.pl/dzielnice"] = "Gdansk/dzielnice.html";
        $pattern = "https://www.gdansk.pl/subpages/dzielnice/html/dzielnice_mapa_alert.php?id=%d";
        for ($i = 1; $i <= 35; $i++) {
            $urlFileMap[sprintf($pattern, $i)] = "Gdansk/dzielnice_mapa_alert.php?id=16";
        }

        $urlFileMap["http://appimeri.um.krakow.pl/app-pub-dzl/pages/DzlViewAll.jsf?a=1&lay=normal&fo=0"] =
            "Krakow/DzlViewAll.jsf?a=1&lay=normal&fo=0";
        $pattern = "http://appimeri.um.krakow.pl/app-pub-dzl/pages/DzlViewGlw.jsf?id=%d&lay=normal&fo=0";
        for ($i = 1; $i <= 18; $i++) {
            $urlFileMap[sprintf($pattern, $i)] = "Krakow/DzlViewGlw.jsf?id=17&lay=normal&fo=0";
        }

        foreach ($urlFileMap as $url => $filename) {
            $this->data[$url] = file_get_contents(__DIR__ . "/data/" . $filename);
        }
    }

    public function fetchHtml(string $url): string
    {
        if (!array_key_exists($url, $this->data)) {
            throw new \LogicException();
        }
        return $this->data[$url];
    }
}
