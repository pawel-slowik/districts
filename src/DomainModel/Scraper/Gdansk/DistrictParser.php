<?php

declare(strict_types=1);

namespace Districts\DomainModel\Scraper\Gdansk;

use Districts\DomainModel\Exception\InvalidHtmlException;
use Districts\DomainModel\Exception\ParsingException;
use Districts\DomainModel\Scraper\DistrictDTO;
use Districts\DomainModel\Scraper\HtmlFinder;

class DistrictParser
{
    private $htmlFinder;

    public function __construct(HtmlFinder $htmlFinder)
    {
        $this->htmlFinder = $htmlFinder;
    }

    public function parse(string $html): DistrictDTO
    {
        // This is an HTML fragment instead of a valid document. Add a charset
        // declaration in order to decode the text properly.
        $fixedHtml = "<html><head><meta charset=\"utf-8\"></head><body>{$html}</body></html>";
        $xpath = "//div[contains(@class, 'opis')]/div";
        try {
            $nodes = $this->htmlFinder->findNodes($fixedHtml, $xpath);
        } catch (InvalidHtmlException $exception) {
            throw new ParsingException();
        }
        if (count($nodes) < 1) {
            throw new ParsingException();
        }
        $texts = array_map(
            function ($node) {
                return $node->textContent;
            },
            $nodes
        );
        $name = $texts[0];
        $area = self::getArea($texts);
        $population = self::getPopulation($texts);
        return new DistrictDTO($name, $area, $population);
    }

    private static function getArea(array $texts): float
    {
        $regexp = "/Powierzchnia:[[:space:]]+([0-9]+(,[0-9]+){0,1})[[:space:]]+km/";
        $match = self::getSingleMatch($texts, $regexp);
        $area = str_replace(",", ".", $match); // decimal point
        return floatval($area);
    }

    private static function getPopulation(array $texts): int
    {
        $regexp = "/Liczba[[:space:]]ludno.+ci:[[:space:]]+([0-9]+)[[:space:]]/";
        $match = self::getSingleMatch($texts, $regexp);
        return intval($match);
    }

    private static function getSingleMatch(array $texts, string $regexp): string
    {
        $allMatches = [];
        foreach ($texts as $text) {
            $currentMatches = [];
            if (preg_match($regexp, $text, $currentMatches)) {
                $allMatches[] = $currentMatches[1];
            }
        }
        if (count($allMatches) !== 1) {
            throw new ParsingException();
        }
        return $allMatches[0];
    }
}
