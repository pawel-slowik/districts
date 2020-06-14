<?php

declare(strict_types=1);

namespace Scraper\City;

use Entity\District;
use Scraper\HtmlFinder;
use Scraper\RuntimeException;
use Scraper\DistrictBuilderBase;
use Validator\DistrictValidator;

final class KrakowDistrictBuilder extends DistrictBuilderBase
{
    private $htmlFinder;

    private $validator;

    public function __construct(HtmlFinder $htmlFinder, DistrictValidator $validator)
    {
        $this->htmlFinder = $htmlFinder;
        $this->validator = $validator;
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
        return $this->createValidatedDistrict(
            $this->validator,
            [
                "name" => $name,
                "area" => $area,
                "population" => $population,
            ]
        );
    }

    private function getSingleItem(string $html, string $xpath, callable $callback)
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

    private function extractName(string $text): ?string
    {
        $text = trim($text);
        $regexp = "/^Dzielnica[[:space:]]+[IVXLCDM]+[[:space:]]+(.*)$/u";
        $matches = [];
        if (!preg_match($regexp, $text, $matches)) {
            return null;
        }
        return $matches[1];
    }

    private function extractArea(string $text): ?float
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

    private function extractPopulation(string $text): ?int
    {
        if (!preg_match("/^[0-9]+$/", $text)) {
            return null;
        }
        return intval($text);
    }
}
