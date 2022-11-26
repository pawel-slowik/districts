<?php

declare(strict_types=1);

namespace Districts\DomainModel\Scraper\Krakow;

use Districts\DomainModel\Scraper\DistrictDTO;
use Districts\DomainModel\Scraper\Exception\InvalidHtmlException;
use Districts\DomainModel\Scraper\Exception\ParsingException;
use Districts\DomainModel\Scraper\HtmlFinder;

class DistrictParser
{
    public function __construct(
        private HtmlFinder $htmlFinder,
    ) {
    }

    public function parse(string $html): DistrictDTO
    {
        $name = $this->getName($html);
        $area = $this->getArea($html);
        $population = $this->getPopulation($html);
        return new DistrictDTO($name, $area, $population);
    }

    private function getName(string $html): string
    {
        $xpath = "//h1[@class='bip' and starts-with(., 'Dzielnica ')]";
        $regexp = "/^[[:space:]]*Dzielnica[[:space:]]+[IVXLCDM]+[[:space:]]+(.*)[[:space:]]*$/uU";
        return $this->getSingleMatch($html, $xpath, $regexp);
    }

    private function getArea(string $html): float
    {
        $xpath = "//p[contains(., 'Powierzchnia dzielnicy:')]/strong";
        $regexp = "/([0-9]+(,[0-9]+){0,1})[[:space:]]+ha/";

        $area = $this->getSingleMatch($html, $xpath, $regexp);
        $area = str_replace(",", ".", $area); // decimal point
        $area = floatval($area);
        $area = $area / 100; // unit conversion: ha to square km

        return $area;
    }

    private function getPopulation(string $html): int
    {
        $xpath = "//p[contains(., 'Liczba mieszka')]/strong";
        $regexp = "/^[[:space:]]*([0-9]+)[[:space:]]*$/";
        $text = $this->getSingleMatch($html, $xpath, $regexp);
        return intval($text);
    }

    private function getSingleMatch(string $html, string $xpath, string $regexp): string
    {
        try {
            $nodes = $this->htmlFinder->findNodes($html, $xpath);
        } catch (InvalidHtmlException $exception) {
            throw new ParsingException();
        }
        if (count($nodes) !== 1) {
            throw new ParsingException();
        }
        $value = $nodes[0]->textContent;
        $matches = [];
        if (!preg_match($regexp, $value, $matches)) {
            throw new ParsingException();
        }
        return $matches[1];
    }
}
