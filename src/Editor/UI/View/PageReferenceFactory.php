<?php

declare(strict_types=1);

namespace Districts\Editor\UI\View;

use Laminas\Uri\Uri;
use Traversable;

class PageReferenceFactory
{
    /**
     * @return Traversable<PageReference>
     */
    public function createPageReferencesForUrl(
        string $baseUrl,
        int $pageCount,
        int $currentPageNumber
    ): Traversable {
        if ($pageCount <= 1) {
            return;
        }
        yield new PageReference(
            ($currentPageNumber === 1) ? null : self::urlForPageNumber($baseUrl, $currentPageNumber - 1),
            "previous",
            false,
            true,
            false,
        );
        foreach (range(1, $pageCount) as $pageNumber) {
            yield new PageReference(
                self::urlForPageNumber($baseUrl, $pageNumber),
                strval($pageNumber),
                $pageNumber === $currentPageNumber,
                false,
                false,
            );
        }
        yield new PageReference(
            ($currentPageNumber === $pageCount) ? null : self::urlForPageNumber($baseUrl, $currentPageNumber + 1),
            "next",
            false,
            false,
            true,
        );
    }

    private static function urlForPageNumber(string $baseUrl, int $pageNumber): string
    {
        $parsedUrl = new Uri($baseUrl);
        $parsedUrl->setQuery(array_merge($parsedUrl->getQueryAsArray(), ["page" => $pageNumber]));
        return $parsedUrl->toString();
    }
}
