<?php

declare(strict_types=1);

namespace Scraper\City;

use Entity\District;
use Scraper\HtmlFinder;
use Scraper\RuntimeException;
use Validator\Validator;

class GdanskDistrictBuilder
{
    protected $htmlFinder;

    protected $validator;

    public function __construct(HtmlFinder $htmlFinder, Validator $validator)
    {
        $this->htmlFinder = $htmlFinder;
        $this->validator = $validator;
    }

    public function buildFromHtml(string $html): District
    {
        // This is an HTML fragment instead of a valid document. Add a charset
        // declaration in order to decode the text properly.
        $fixedHtml = "<html><head><meta charset=\"utf-8\"></head><body>{$html}</body></html>";
        $xpath = "//div[contains(@class, 'opis')]/div";
        $nodes = $this->htmlFinder->findNodes($fixedHtml, $xpath);
        if (count($nodes) < 1) {
            throw new RuntimeException();
        }
        $texts = array_map(
            function ($node) {
                return $node->textContent;
            },
            $nodes
        );
        $name = $texts[0];
        $area = $this->findSingleItem($texts, [$this, "extractArea"]);
        $population = $this->findSingleItem($texts, [$this, "extractPopulation"]);
        $result = $this->validator->validate([
            "name" => $name,
            "area" => $area,
            "population" => $population,
        ]);
        if (!$result->isOk()) {
            throw new RuntimeException(
                "validation failed: " . implode(", ", array_map("strval", $result->getErrors()))
            );
        }
        $validated = $result->getValidatedData();
        return new District($validated["name"], $validated["area"], $validated["population"]);
    }

    protected function findSingleItem(array $texts, callable $callback)
    {
        $values = $this->filterNulls(array_map($callback, $texts));
        if (count($values) !== 1) {
            throw new RuntimeException();
        }
        return array_values($values)[0];
    }

    protected function extractArea(string $text): ?float
    {
        $regexp = "/Powierzchnia:[[:space:]]+([0-9]+(,[0-9]+){0,1})[[:space:]]+km/";
        $matches = [];
        if (!preg_match($regexp, $text, $matches)) {
            return null;
        }
        $area = str_replace(",", ".", $matches[1]); // decimal point
        return floatval($area);
    }

    protected function extractPopulation(string $text): ?int
    {
        $regexp = "/Liczba[[:space:]]ludno.+ci:[[:space:]]+([0-9]+)[[:space:]]/";
        $matches = [];
        if (!preg_match($regexp, $text, $matches)) {
            return null;
        }
        return intval($matches[1]);
    }

    protected function filterNulls(array $values): array
    {
        return array_filter(
            $values,
            function ($value) {
                return !is_null($value);
            }
        );
    }
}
