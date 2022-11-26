<?php

declare(strict_types=1);

namespace Districts\Domain\Scraper;

use Districts\Domain\Scraper\Exception\InvalidHtmlException;
use Districts\Domain\Scraper\Exception\InvalidQueryException;
use DOMDocument;
use DOMXpath;
use ValueError;

class HtmlFinder
{
    public function findNodes(string $html, string $xpath): array
    {
        $document = new DOMDocument();
        try {
            // @ silence warnings for mismatched HTML tags etc.
            if (!@$document->loadHTML($html)) {
                throw new InvalidHtmlException();
            }
        } catch (ValueError $error) {
            throw new InvalidHtmlException();
        }
        $nodes = (new DOMXpath($document))->query($xpath);
        if ($nodes === false) {
            throw new InvalidQueryException();
        }
        return iterator_to_array($nodes);
    }
}
