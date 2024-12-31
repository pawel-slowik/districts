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
        yield PageReference::forPrevious(
            ($currentPageNumber === 1) ? null : self::urlForPageNumber($baseUrl, $currentPageNumber - 1),
        );
        foreach (range(1, $pageCount) as $pageNumber) {
            yield PageReference::forNumber(
                self::urlForPageNumber($baseUrl, $pageNumber),
                $pageNumber,
                $pageNumber === $currentPageNumber,
            );
        }
        yield PageReference::forNext(
            ($currentPageNumber === $pageCount) ? null : self::urlForPageNumber($baseUrl, $currentPageNumber + 1),
        );
    }

    private static function urlForPageNumber(string $baseUrl, int $pageNumber): string
    {
        $parsedUrl = new Uri($baseUrl);
        $parsedUrl->setQuery(array_merge($parsedUrl->getQueryAsArray(), ["page" => $pageNumber]));
        return $parsedUrl->toString();
    }
}
