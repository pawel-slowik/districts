<?php

declare(strict_types=1);

namespace Districts\Editor\UI\View;

use Psr\Http\Message\UriInterface;
use Traversable;

class PageReferenceFactory
{
    /**
     * @return Traversable<PageReference>
     */
    public function createPageReferencesForUrl(
        UriInterface $baseUrl,
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

    private static function urlForPageNumber(UriInterface $baseUrl, int $pageNumber): string
    {
        $queryArgs = [];
        parse_str($baseUrl->getQuery(), $queryArgs);
        $queryArgs["page"] = $pageNumber;
        return (string) $baseUrl->withQuery(http_build_query($queryArgs));
    }
}
