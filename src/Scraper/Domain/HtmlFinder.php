<?php

declare(strict_types=1);

namespace Districts\Scraper\Domain;

use Districts\Scraper\Domain\Exception\InvalidHtmlException;
use Districts\Scraper\Domain\Exception\InvalidQueryException;
use DOMDocument;
use DOMNode;
use DOMXPath;
use ValueError;

class HtmlFinder
{
    /**
     * @return DOMNode[]
     */
    public function findNodes(string $html, string $xpath): array
    {
        $document = new DOMDocument();
        try {
            if (!$document->loadHTML($html, LIBXML_NOWARNING | LIBXML_NOERROR)) {
                throw new InvalidHtmlException();
            }
        } catch (ValueError) {
            throw new InvalidHtmlException();
        }
        $nodes = new DOMXPath($document)->query($xpath);
        if ($nodes === false) {
            throw new InvalidQueryException();
        }
        return iterator_to_array($nodes);
    }

    public function getAttribute(DOMNode $node, string $attribute): string
    {
        if ($node->attributes === null) {
            throw new InvalidHtmlException();
        }
        for ($i = 0; $i < $node->attributes->length; $i++) {
            $attributeNode = $node->attributes->item($i);
            if ($attributeNode === null) {
                throw new InvalidHtmlException();
            }
            if ($attributeNode->nodeName === $attribute) {
                if ($attributeNode->nodeValue === null) {
                    continue;
                }
                return $attributeNode->nodeValue;
            }
        }

        throw new InvalidHtmlException();
    }
}
