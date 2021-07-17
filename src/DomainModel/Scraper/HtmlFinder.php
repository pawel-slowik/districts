<?php

declare(strict_types=1);

namespace Districts\DomainModel\Scraper;

use DOMDocument;
use DOMXpath;

class HtmlFinder
{
    public function findNodes(string $html, string $xpath): array
    {
        $document = new DOMDocument();
        // @ silence warnings for mismatched HTML tags etc.
        if (!@$document->loadHTML($html)) {
            throw new RuntimeException();
        }
        $nodes = (new DOMXpath($document))->query($xpath);
        if ($nodes === false) {
            throw new RuntimeException();
        }
        return iterator_to_array($nodes);
    }
}
