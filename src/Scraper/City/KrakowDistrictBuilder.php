<?php

declare(strict_types=1);

namespace Scraper\City;

use District;
use Scraper\HtmlFinder;

class KrakowDistrictBuilder
{
    protected $htmlFinder;

    public function __construct(HtmlFinder $htmlFinder)
    {
        $this->htmlFinder = $htmlFinder;
    }

    public function buildFromHtml(string $html): District
    {
        $name = $this->getSingleItem(
            $html,
            "//h3",
            [$this, "extractName"]
        );
        $area = $this->getSingleItem(
            $html,
            "//td/b[.='Powierzchnia:']/../following-sibling::td",
            [$this, "extractArea"]
        );
        $population = $this->getSingleItem(
            $html,
            "//td/b[contains(., 'Liczba ludno')]/../following-sibling::td",
            [$this, "extractPopulation"]
        );
        return new District($name, $area, $population);
    }

    protected function getSingleItem(string $html, string $xpath, callable $callback)
    {
        $nodes = $this->htmlFinder->findNodes($html, $xpath);
        if (count($nodes) !== 1) {
            throw new RuntimeException();
        }
        $value = $callback($nodes[0]->textContent);
        if (is_null($value)) {
            throw new RuntimeException();
        }
        return $value;
    }

    protected function extractName(string $text): ?string
    {
        $text = trim($text);
        $regexp = "/^Dzielnica[[:space:]]+[IVXLCDM]+[[:space:]]+(.*)$/u";
        $matches = [];
        if (!preg_match($regexp, $text, $matches)) {
            return null;
        }
        return $matches[1];
    }

    protected function extractArea(string $text): ?float
    {
        $text = trim($text);
        $regexp = "/([0-9]+(,[0-9]+){0,1})[[:space:]]+ha/";
        $matches = [];
        if (!preg_match($regexp, $text, $matches)) {
            return null;
        }
        $area = str_replace(",", ".", $matches[1]); // decimal point
        $area = floatval($area);
        $area = $area / 100; // unit conversion: ha to square km
        return $area;
    }

    protected function extractPopulation(string $text): ?int
    {
        if (!preg_match("/^[0-9]+$/", $text)) {
            return null;
        }
        return intval($text);
    }
}
