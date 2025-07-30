<?php

declare(strict_types=1);

namespace Districts\Scraper\Domain\Gdansk;

use Districts\Scraper\Domain\DistrictDTO;
use Districts\Scraper\Domain\Exception\InvalidHtmlException;
use Districts\Scraper\Domain\Exception\ParsingException;
use Districts\Scraper\Domain\HtmlFinder;

readonly class DistrictParser
{
    public function __construct(
        private HtmlFinder $htmlFinder,
    ) {
    }

    public function parse(string $html): DistrictDTO
    {
        // This is an HTML fragment instead of a valid document. Add a charset
        // declaration in order to decode the text properly.
        $fixedHtml = "<html><head><meta charset=\"utf-8\"></head><body>{$html}</body></html>";
        $xpath = "//div[contains(@class, 'opis')]/div";
        try {
            $nodes = $this->htmlFinder->findNodes($fixedHtml, $xpath);
        } catch (InvalidHtmlException) {
            throw new ParsingException();
        }
        if (count($nodes) < 1) {
            throw new ParsingException();
        }
        $texts = array_map(
            static fn ($node) => $node->textContent,
            $nodes
        );
        $name = $texts[0];
        $area = self::getArea($texts);
        $population = self::getPopulation($texts);
        return new DistrictDTO($name, $area, $population);
    }

    /**
     * @param string[] $texts
     */
    private static function getArea(array $texts): float
    {
        $regexp = "/Powierzchnia:[[:space:]]+([0-9]+(,[0-9]+){0,1})[[:space:]]+km/";
        $match = self::getSingleMatch($texts, $regexp);
        $area = str_replace(",", ".", $match); // decimal point
        return floatval($area);
    }

    /**
     * @param string[] $texts
     */
    private static function getPopulation(array $texts): int
    {
        $regexp = "/Liczba[[:space:]]ludno.+ci:[[:space:]]+([0-9]+)[[:space:]]/";
        $match = self::getSingleMatch($texts, $regexp);
        return intval($match);
    }

    /**
     * @param string[] $texts
     */
    private static function getSingleMatch(array $texts, string $regexp): string
    {
        $allMatches = [];
        foreach ($texts as $text) {
            $currentMatches = [];
            if (preg_match($regexp, $text, $currentMatches) === 1) {
                $allMatches[] = $currentMatches[1];
            }
        }
        if (count($allMatches) !== 1) {
            throw new ParsingException();
        }
        return $allMatches[0];
    }
}
